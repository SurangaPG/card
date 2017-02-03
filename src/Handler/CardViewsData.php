<?php

namespace Drupal\card\Handler;

use Drupal\views\EntityViewsData;
use Drupal\views\EntityViewsDataInterface;

/**
 * Provides Views data for Card entities.
 */
class CardViewsData extends EntityViewsData implements EntityViewsDataInterface {
  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['card']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Card'),
      'help' => $this->t('The Card ID.'),
    );

    return $data;
  }

}
