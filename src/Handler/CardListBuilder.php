<?php

namespace Drupal\card\Handler;

use Drupal\card\CardInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityListBuilder;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Routing\LinkGeneratorTrait;
use Drupal\Core\Url;

/**
 * Defines a class to build a listing of Card entities.
 *
 * @ingroup card
 */
class CardListBuilder extends EntityListBuilder {
  use LinkGeneratorTrait;
  /**
   * {@inheritdoc}
   */
  public function buildHeader() {

    $header = [
      t('Label'),
      t('Region'),
    ];
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {

    /** @var CardInterface $entity */
    $row = [
      $entity->label(),
      $entity->getRegion(),
    ];
    return $row + parent::buildRow($entity);
  }

}
