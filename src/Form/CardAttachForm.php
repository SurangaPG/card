<?php

namespace Drupal\card\Form;

use Drupal\card\CardInterface;
use Drupal\card\CardRepository;
use Drupal\card\Entity\Card;
use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Routing\RedirectDestination;

/**
 * Form controller for Card attach forms. These are used to add a card to the
 * data contained in the GET parameters in the url.
 * @TODO This is a bit dirty but I can't see a better way to add params directly
 *
 * @ingroup card
 */
class CardAttachForm extends CardForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


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

    if(isset($_GET['canonical'])) {
      $parameters = !empty($_GET['route_params']) ?
        CardRepository::decodeParameterString($_GET['route_params']) :
        [];
      $form_state->setRedirect($_GET['canonical'], $parameters);
    }
    else {
      $form_state->setRedirect('entity.card.canonical', ['card' => $entity->id()]);
    }
  }
}
