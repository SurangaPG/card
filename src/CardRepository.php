<?php

namespace Drupal\card;

use Drupal\card\Entity\Card;
use Drupal\card\Event\CardRegionBuildEvent;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Drupal\Core\Url;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CardRepository implements CardRepositoryInterface {

  /**
   * Card build event for a region.
   */
  const CARD_BUILD_REGION_EVENT = 'card.build_region';

  /**
   * The theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * The entity type manage
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The event dispatcher
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * Constructs a new BlockRepository.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity manager.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher
   *   The event dispatcher
   */
  public function __construct(
    EntityTypeManagerInterface $entity_type_manager,
    ThemeManagerInterface $theme_manager,
    EventDispatcherInterface $event_dispatcher
  ) {
    $this->entityTypeManager = $entity_type_manager;
    $this->themeManager = $theme_manager;
    $this->eventDispatcher = $event_dispatcher;
  }

  /**
   * @inheritdoc
   */
  public static function generateParameterString($route_match) {
    $raw_parameters = $route_match->getRawParameters()->all();

    if (empty($raw_parameters)) {
      return null;
    }

    asort($raw_parameters);

    $parameters = [];
    foreach ($raw_parameters as $key => $parameter) {
      $parameters[] .= $key . ':' . $parameter;
    }

    return implode('|', $parameters);
  }

  /**
   * @inheritdoc
   */
  public static function decodeParameterString($parameter_string) {
    $params = explode('|', $parameter_string);
    $param_array = [];

    foreach($params as $param) {
      list($key, $value) = explode(':', $param);
      $param_array[$key] = $value;
    }

    return $param_array;
  }

  /**
   * @inheritdoc
   */
  public function generateRegionAttachLink($route_match, $region) {

    // @TODO currently here for possible future reference, but might proof obselete if everything can be passed in the route.

    /** @var \Drupal\Core\Theme\ThemeManagerInterface $themeManager */
    // $themeManager = Drupal::service('theme.manager');

    $url = Url::fromRoute('entity.card.attach_form');
    $url->setOption('query', [
        'canonical' => $route_match->getRouteName(),
        'route_params' => static::generateParameterString($route_match),
        'theme' => $this->themeManager->getActiveTheme()->getName(),
        'region' => $region,
      ]
    );
    $link = Link::fromTextAndUrl("Attach a card to this region", $url);
    $page[] = $link->toRenderable();

    return $link->toRenderable();
  }

  /**
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *
   * @return array
   */
  public function getCardsFromRouteMatch(RouteMatchInterface $route_match) {

    /**
     * @TODO Reduce the number of db queries
     * We'll load all the cards in one go as much as possible to increase
     * make it easier to cache data later and reduce the number of DB calls.
     */

    /*
     * @TODO This is a temporary implementation
     * We'll dispatch an event to join in more flexible content with the
     * more config oriented blocks in the region.
     * Basically we'll just allow any module to decide how it adds which content
     * to the flexible card system based on the route that is currently being
     * loaded
     */
    $cardsPerRegion = [];
    foreach($this->themeManager->getActiveTheme()->getRegions() as $region) {
      $event = new CardRegionBuildEvent($region, $route_match);
      $this->eventDispatcher->dispatch(self::CARD_BUILD_REGION_EVENT, $event);

      /*
       * Inject the newly added data into the region. This will allow the card
       * to be rendered into the same array as the block.
       *
       * @TODO Should there be a way to prevent the same card loaded from different sources collapsing on each other
       */
      $loaders = $this->generateCardLoaders($event->getCards());

      $cardsPerRegion[$region] = $loaders;
    }

    return $cardsPerRegion;
  }

  /**
   * Generate lazy loaders based on the data passed from the event dispatcher.
   *
   * @param array $cardData
   *    An array of card data to display the cards from. It has the keys
   *      id, view mode (optional), language (optional).
   * @return array
   *    Array of card lazy loaders keyed by machine name.
   */
  protected function generateCardLoaders($cardData) {
    $loaders = [];

    $cards = $this->loadRelevantCards($cardData);

    foreach($cardData as $card) {
      $loaders['card_' . $card['id']] = [
        '#lazy_builder' => [
          "Drupal\\card\\Handler\\CardViewBuilder::lazyBuilder",
          [
            $card['id'],
            isset($card['view_mode']) ? $card['view_mode'] : null,
            isset($card['language']) ? $card['language'] : null,
          ]
        ],
        '#weight' => isset($cardData['#weight']) ?
          $cardData['#weight'] : $cards[$card['id']]->getWeight(),
      ];
    }

    return $loaders;
  }

  /**
   * Load all the cards to get their weight.
   *
   * @TODO This is a bit unfortunate since we don't really want to do yet.
   * But we need the weights and there is no real clean way to load these.
   *
   * @param array $cardData
   *   The card data returned from the query.
   *
   * @return CardInterface[] $cards
   *   All the relevant cards for this region.
   */
  protected function loadRelevantCards($cardData) {
    $cards = [];
    foreach($cardData as $card) {
      $cards[$card['id']] = $card['id'];
    }
    if(!empty($cards)) {
      $cards = Card::loadMultiple($cards);
    }

    return $cards;
  }
}