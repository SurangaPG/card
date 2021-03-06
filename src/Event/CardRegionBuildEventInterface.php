<?php

namespace Drupal\card\Event;

interface CardRegionBuildEventInterface {

  /**
   * Gets the machine name of the event that is currently being rendered.
   * @return string
   */
  public function getRegion();

  /**
   * Route match for the current request.
   * @return \Drupal\Core\Routing\RouteMatchInterface
   */
  public function getRouteMatch();

  /**
   * @return array
   */
  public function getCards();

  /**
   * @param $cards
   */
  public function setCards($cards);

  /**
   * Adds a card based on it's id
   * @param array $cardData An array of lazy loadable card data for this region.
   *  has the id, view_mode (optional) and language (optional) keys.
   */
  public function addCard(array $cardData);

}