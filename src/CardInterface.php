<?php

namespace Drupal\card;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Card entities.
 *
 * @ingroup card
 */
interface CardInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * @return string
   *  The canonical name for the route this card is attached to.
   */
  public function getCanonical();

  /**
   * Sets the region this Card appears in.
   *
   * @param string $canonical
   *  The canonical route for the card.
   *
   * @return CardInterface
   *  The card
   */
  public function setCanonical($canonical);

  /**
   * @return string
   *  The params of the route in the form
   *  |paramName:paramValue|paramName:paramValue ...
   */
  public function getRouteParams();

  /**
   * Sets the region this Card appears in.
   *
   * @param string $params
   *  An imploded string for all the params. These should be sorted alphabetically
   *  and then placed in the form.
   *  |paramName:paramValue|paramName:paramValue ...
   *
   * @return CardInterface
   *  The card
   */
  public function setRouteParams($params);

  /**
   * @return string
   *   The machine name of the region this card appears in.
   */
  public function getRegion();

  /**
   * Sets the region this Card appears in.
   *
   * @param string $region
   *  The machine name for the region.
   *
   * @return CardInterface
   *  The card
   */
  public function setRegion($region);

  /**
   * Gets the Card creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Card.
   */
  public function getCreatedTime();

  /**
   * Sets the Card creation timestamp.
   *
   * @param int $timestamp
   *   The Card creation timestamp.
   *
   * @return \Drupal\card\CardInterface
   *   The called Card entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Returns the Card published status indicator.
   *
   * Unpublished Card are only visible to restricted users.
   *
   * @return bool
   *   TRUE if the Card is published.
   */
  public function isPublished();

  /**
   * Sets the published status of a Card.
   *
   * @param bool $published
   *   TRUE to set this Card to published, FALSE to set it to unpublished.
   *
   * @return \Drupal\card\CardInterface
   *   The called Card entity.
   */
  public function setPublished($published);

}
