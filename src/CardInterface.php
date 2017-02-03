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
