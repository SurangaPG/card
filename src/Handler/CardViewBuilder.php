<?php

namespace Drupal\card\Handler;

use Drupal\Core\Block\MainContentBlockPluginInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManager;
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
  }
}
