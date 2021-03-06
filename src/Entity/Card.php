<?php

namespace Drupal\card\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\card\CardInterface;
use Drupal\user\UserInterface;
use Symfony\Component\Routing\Route;

/**
 * Defines the Card entity.
 *
 * @ingroup card
 *
 * @ContentEntityType(
 *   id = "card",
 *   label = @Translation("Card"),
 *   handlers = {
 *     "view_builder" = "Drupal\card\Handler\CardViewBuilder",
 *     "list_builder" = "Drupal\card\Handler\CardListBuilder",
 *     "views_data" = "Drupal\card\Handler\CardViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\card\Form\CardForm",
 *       "add" = "Drupal\card\Form\CardForm",
 *       "attach" = "Drupal\card\Form\CardAttachForm",
 *       "edit" = "Drupal\card\Form\CardForm",
 *       "delete" = "Drupal\card\Form\CardDeleteForm",
 *     },
 *     "access" = "Drupal\card\Handler\CardAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\card\RouteProvider\CardHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "card",
 *   admin_permission = "administer card entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "uuid",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "status" = "status",
 *   },
 *   links = {
 *     "canonical" = "/admin/content/card/{card}",
 *     "add-form" = "/admin/content/card/add",
 *     "attach-form" = "/admin/content/card/attach",
 *     "edit-form" = "/admin/content/card/{card}/edit",
 *     "layout-form" = "/admin/content/card/{card}/layout",
 *     "delete-form" = "/admin/content/card/{card}/delete",
 *     "collection" = "/admin/content/card",
 *   },
 *   field_ui_base_route = "card.settings"
 * )
 */
class Card extends ContentEntityBase implements CardInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += array(
      'user_id' => \Drupal::currentUser()->id(),
    );
  }

  /**
   * Accounts for the fact that the content is in a content_block.
   * @inheritdoc
   */
  public function label() {
    if($this->getBlockContent()) {
      return $this->getBlockContent()->label();
    }
    return parent::label();
  }

  /**
   * @inheritdoc
   */
  public function getCanonical() {
    return $this->get('canonical')->value;
  }

  /**
   * @return \Drupal\block_content\BlockContentInterface;
   */
  public function getBlockContent() {
    return $this->get('block_content')->entity;
  }

  /**
   * @param int $blockContentId
   */
  public function setBlockContent($blockContentId) {
    $this->setBlockContent($blockContentId);
  }

  /**
   * @inheritdoc
   */
  public function setCanonical($canonical) {
    $this->set('canonical', $canonical);
    return $this;
  }

  /**
   * @inheritdoc
   */
  public function getRouteParams() {
    return $this->get('route_params')->value;
  }

  /**
   * @inheritdoc
   */
  public function setRouteParams($params) {
    $this->set('route_params', $params);
    return $this;
  }

  /**
   * @inheritdoc
   */
  public function getWeight() {
    $weight = $this->get('weight')->value;
    return isset($weight) ? $weight : 0;
  }

  /**
   * @inheritdoc
   */
  public function setWeight($params) {
    $this->set('weight', $params);
    return $this;
  }


  /**
   * @inheritdoc
   */
  public function getRegion() {
    return $this->get('region')->value;
  }

  /**
   * @inheritdoc
   */
  public function setRegion($region) {
    $this->set('region', $region);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function isPublished() {
    return (bool) $this->getEntityKey('status');
  }

  /**
   * {@inheritdoc}
   */
  public function setPublished($published) {
    $this->set('status', $published ? NODE_PUBLISHED : NODE_NOT_PUBLISHED);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('The ID of the Card entity.'))
      ->setReadOnly(TRUE);

    $fields['canonical'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Canonical'))
      ->setDescription(t('The canonical route name this card appears on'))
      ->setSetting('max_length', 255)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['block_content'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Block Content'))
      ->setDescription(t('The block content id connected to this card.'))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setSetting('target_type', 'block_content');

    $fields['route_params'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Route parameters'))
      ->setDescription(t('The additional params this card appears on.'))
      ->setSetting('max_length', 510)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['weight'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Weight'))
      ->setDescription(t("The weight for the card."))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['region'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Region'))
      ->setDescription(t('The region this card appears in.'))
      ->setSetting('max_length', 255)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['theme'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Theme'))
      ->setDescription(t('The theme this card is active for.'))
      ->setSetting('max_length', 255)
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the Card entity.'))
      ->setReadOnly(TRUE);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Card entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDefaultValueCallback('Drupal\node\Entity\Node::getCurrentUserId')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', array(
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => array(
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ),
      ))
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Publishing status'))
      ->setDescription(t('A boolean indicating whether the Card is published.'))
      ->setDefaultValue(TRUE);

    $fields['langcode'] = BaseFieldDefinition::create('language')
      ->setLabel(t('Language code'))
      ->setDescription(t('The language code for the Card entity.'))
      ->setDisplayOptions('form', array(
        'type' => 'language_select',
        'weight' => 10,
      ))
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
