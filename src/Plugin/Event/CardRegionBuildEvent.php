<?php

namespace Drupal\Card\Event;

use Drupal\Core\Routing\RouteMatchInterface;
use Symfony\Component\EventDispatcher\Event;

class CardRegionBuildEvent extends Event {

  /**
   * @var string $region the region that is being generated
   */
  protected $region;

  /**
   * @var RouteMatchInterface $routeMatch
   */
  protected  $routeMatch;

  /**
   * An array of all the cards that should be added to this region.
   * @var array
   */
  protected $cards;


  /**
   * CardRegionBuildEvent constructor.
   * @param $region
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   */
  public function __construct($region, RouteMatchInterface $routeMatch) {
    $this->region = $region;
    $this->routeMatch = $routeMatch;
    $this->cards = [];
  }

  /**
   * Gets the machine name of the event that is currently being rendered.
   * @return string
   */
  public function getRegion() {
    return $this->region;
  }

  /**
   * Route match for the current request.
   * @return \Drupal\Core\Routing\RouteMatchInterface
   */
  public function getRouteMatch() {
    return $this->routeMatch;
  }

  /**
   * @return array
   */
  public function getCards() {
    return $this->cards;
  }

  /**
   * @param $cards
   */
  public function setCards($cards) {
    $this->cards = $cards;
  }

  /**
   * Adds a card based on it's id
   * @param $cardId
   */
  public function addCard($cardId) {
    $this->cards[$cardId] = $cardId;
  }

}