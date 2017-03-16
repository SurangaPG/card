<?php

namespace Drupal\card\EventSubscriber;

use Drupal\card\CardRepository;
use Drupal\card\Event\CardRegionBuildEventInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CardRegionBuildEventSubscriber implements EventSubscriberInterface {

  /**
   * @param \Drupal\card\Event\CardRegionBuildEventInterface $event
   */
  public function addFromRouteMatch(CardRegionBuildEventInterface $event) {

    // Query all the items matching the route
    /** @var EntityTypeManagerInterface $entityTypeManager */
    $entityTypeManager = \Drupal::service('entity_type.manager');
    $query = $entityTypeManager->getStorage('card')->getQuery();

    $query->condition('region', $event->getRegion())
      ->condition('canonical', $event->getRouteMatch()->getRouteName())
      ->sort('weight', 'ASC');

    // Only add the params as an option if params have been added. Since it
    // ignores NULL values it set with an empty string?
    // @TODO Find some DB guru and have him/her figure out how to clean this up?
    $params = CardRepository::generateParameterString($event->getRouteMatch());
    if (isset($params)) {
      $query->condition('route_params', $params);
    }

    $results = $query->execute();

    foreach($results as $result) {
      $event->addCard(['id' => $result]);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Add the pages based on the current node.
    $events[CardRepository::CARD_BUILD_REGION_EVENT][] = ['addFromRouteMatch', 75];
    return $events;
  }
}