<?php

namespace Drupal\card\Form;

use Drupal\card\Entity\Card;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Form controller for Card edit forms.
 *
 * @ingroup card
 */
class CardForm extends ContentEntityForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    /** @var CardInterface $entity */
    $entity = $this->getEntity();

    if($this->getEntity()->isNew()) {
      $this->entity->setCanonical(isset($_GET['canonical']) ? $_GET['canonical'] : '')
        ->setRegion(isset($_GET['region']) ? $_GET['region'] : '')
        ->setRouteParams(isset($_GET['route_params']) ? $_GET['route_params'] : '');
    }

    $form = parent::buildForm($form, $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /* @var Card $entity */
    $entity = $this->entity;
    $status = parent::save($form, $form_state);

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Card.', [
          '%label' => $entity->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Card.', [
          '%label' => $entity->label(),
        ]));
    }
  }
}
