<?php

namespace Drupal\card\Plugin\DisplayVariant;

use Drupal\block\BlockRepositoryInterface;
use Drupal\block\Plugin\DisplayVariant\BlockPageVariant;
use Drupal\card\Event\CardRegionBuildEvent;
use Drupal\Core\Entity\EntityViewBuilderInterface;
use Drupal\Core\Routing\RedirectDestinationInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Theme\ThemeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Allows blocks to be placed directly within a region.
 *
 * @PageDisplayVariant(
 *   id = "card_page",
 *   admin_label = @Translation("Page with cards instead of blocks")
 * )
 */
class CardPageVariant extends BlockPageVariant {

  /**
   * Card build event for a region. This allows the loading of cards into the
   * system via a standardized event.
   */
  const CARD_BUILD_REGION_EVENT = 'card.build_region';

  /**
   * The theme manager.
   *
   * @var \Drupal\Core\Theme\ThemeManagerInterface
   */
  protected $themeManager;

  /**
   * The redirect destination.
   *
   * @var \Drupal\Core\Routing\RedirectDestinationInterface
   */
  protected $redirectDestination;

  /**
   * The event dispatcher
   *
   * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
   */
  protected $eventDispatcher;

  /**
   * @var \Drupal\Core\Routing\RouteMatchInterface $route_match
   */
  protected $routeMatch;

  /**
   * Constructs a new PlaceBlockPageVariant.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin ID for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\block\BlockRepositoryInterface $block_repository
   *   The block repository.
   * @param \Drupal\Core\Entity\EntityViewBuilderInterface $block_view_builder
   *   The block view builder.
   * @param string[] $block_list_cache_tags
   *   The Block entity type list cache tags.
   * @param \Drupal\Core\Theme\ThemeManagerInterface $theme_manager
   *   The theme manager.
   * @param \Drupal\Core\Routing\RedirectDestinationInterface $redirect_destination
   *   The redirect destination.
   * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface
   *   The symfony event dispatcher
   * @param \Drupal\Core\Routing\RouteMatchInterface $route_match
   *   The route match for the current route
   */
  public function __construct(
    array $configuration,
    $plugin_id, $plugin_definition,
    BlockRepositoryInterface $block_repository,
    EntityViewBuilderInterface $block_view_builder,
    array $block_list_cache_tags,
    ThemeManagerInterface $theme_manager,
    RedirectDestinationInterface $redirect_destination,
    EventDispatcherInterface $event_dispatcher,
    RouteMatchInterface $route_match
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $block_repository, $block_view_builder, $block_list_cache_tags);

    $this->themeManager = $theme_manager;
    $this->redirectDestination = $redirect_destination;
    $this->eventDispatcher = $event_dispatcher;
    $this->routeMatch = $route_match;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('block.repository'),
      $container->get('entity_type.manager')->getViewBuilder('block'),
      $container->get('entity_type.manager')->getDefinition('block')->getListCacheTags(),
      $container->get('theme.manager'),
      $container->get('redirect.destination'),
      $container->get('event_dispatcher'),
      $container->get('current_route_match')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();

    /*
     * We'll dispatch an event to join in more flexible content with the
     * more config oriented blocks in the region.
     * Basically we'll just allow any module to decide how it adds which content
     * to the flexible card system based on the route that is currently being
     * loaded
     */
    foreach($this->themeManager->getActiveTheme()->getRegions() as $region) {
      $event = new CardRegionBuildEvent($region, $this->routeMatch);
      $this->eventDispatcher->dispatch(self::CARD_BUILD_REGION_EVENT, $event);

      /*
       * Inject the newly added data into the region. This will allow the card
       * to be rendered into the same array as the block.
       *
       * @TODO Should there be a way to prevent the same card loaded from different sources collapsing on each other
       */
      $loaders = $this->generateCardLoaders($event->getCards());

      /*
       * Merge the data from the loaders into the block regions.
       */
      $build[$region] = array_merge($build[$region], $loaders);
      $build[$region]['#sorted'] = FALSE;

    }
    return $build;
  }

  /**
   * Generate lazy loaders based on the data passed from the event dispatcher.
   *
   * @param array $cardData
   *    An array of card data to display the cards from. Either an array of data
   *    with extra keys or a simple id.
   * @return array
   *    Array of card lazy loaders keyed by machine name.
   */
  protected function generateCardLoaders($cardData) {
    $loaders = [];

    foreach($cardData as $card) {
      $loaders['card_' . $card['id']] = [
        '#lazy_builder' => [
          "Drupal\\card\\Handler\\CardViewBuilder::lazyBuilder",
          [
            $card['id'],
            isset($card['view_mode']) ? $card['view_mode'] : null,
            isset($card['language']) ? $card['language'] : null,
          ]
        ]
      ];
    }

    return $loaders;
  }

  /**
   * Returns the human-readable list of regions keyed by machine name.
   *
   * @param string $theme
   *   The name of the theme.
   *
   * @return array
   *   An array of human-readable region names keyed by machine name.
   */
  protected function getVisibleRegionNames($theme) {
    return system_region_list($theme, REGIONS_VISIBLE);
  }

}
