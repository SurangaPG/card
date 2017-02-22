<?php

namespace Drupal\card\Plugin\DisplayVariant;

use Drupal\block\BlockRepositoryInterface;
use Drupal\block\Plugin\DisplayVariant\BlockPageVariant;
use Drupal\card\CardRepositoryInterface;
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
   * @var \Drupal\card\CardRepositoryInterface $cardRepository
   */
  protected $cardRepository;

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
   * @param \Drupal\card\CardRepositoryInterface $card_repository
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
    RouteMatchInterface $route_match,
    CardRepositoryInterface $card_repository
  ) {
    parent::__construct(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $block_repository,
      $block_view_builder,
      $block_list_cache_tags
    );

    $this->themeManager = $theme_manager;
    $this->redirectDestination = $redirect_destination;
    $this->eventDispatcher = $event_dispatcher;
    $this->routeMatch = $route_match;
    $this->cardRepository = $card_repository;
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
      $container->get('current_route_match'),
      $container->get('card.repository')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();

    $cardsPerRegion = $this->cardRepository->getCardsFromRouteMatch($this->routeMatch);

    foreach($cardsPerRegion as $region => $cards) {

      /*
       * Merge the data from the loaders into the block regions.
       * Taking into account that certain arrays might have been empty.
       */
      $build[$region] = isset($build[$region]) ?
        array_merge($build[$region], $cards) :
        $cards
      ;
      $build[$region]['#sorted'] = FALSE;

      $build[$region]['card_region_controls'] = $this->cardRepository->generateRegionAttachLink($this->routeMatch, $region);
    }

    return $build;
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
