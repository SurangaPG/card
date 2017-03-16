<?php

namespace Drupal\card\Handler;

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\card\Entity\Card;

/**
 * View builder handler for nodes.
 */
class CardViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  protected function alterBuild(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display, $view_mode) {

    /** @var \Drupal\card\CardInterface $entity */
    parent::alterBuild($build, $entity, $display, $view_mode);

    $build['#weight'] = $entity->get('weight');
  }

  /**
   * @param $id
   * @param $view_mode
   * @param $language
   * @return array
   *
   * @TODO is there a pattern where we don't load the entity_type.manager service multiple times.
   */
  public static function lazyBuilder($id, $view_mode, $language) {
    /** @var EntityTypeManagerInterface $manager */
    $manager = \Drupal::service('entity_type.manager');
    return $manager->getViewBuilder('card')->view(Card::load($id), $view_mode, $language);
  }
}
