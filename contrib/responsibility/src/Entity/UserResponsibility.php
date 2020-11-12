<?php

namespace Drupal\responsibility\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the User responsibility entity.
 *
 * @ingroup responsibility
 *
 * @ContentEntityType(
 *   id = "user_responsibility",
 *   label = @Translation("User responsibility"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\responsibility\UserResponsibilityListBuilder",
 *     "views_data" = "Drupal\responsibility\Entity\UserResponsibilityViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\responsibility\Form\UserResponsibilityForm",
 *       "add" = "Drupal\responsibility\Form\UserResponsibilityForm",
 *       "edit" = "Drupal\responsibility\Form\UserResponsibilityForm",
 *       "delete" = "Drupal\responsibility\Form\UserResponsibilityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\responsibility\UserResponsibilityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\responsibility\UserResponsibilityAccessControlHandler",
 *   },
 *   base_table = "user_responsibility",
 *   translatable = FALSE,
 *   admin_permission = "administer user responsibilities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/user_responsibility/{user_responsibility}",
 *     "add-form" = "/user_responsibility/add",
 *     "edit-form" = "/user_responsibility/{user_responsibility}/edit",
 *     "delete-form" = "/user_responsibility/{user_responsibility}/delete",
 *     "collection" = "/user_responsibility",
 *   },
 *   field_ui_base_route = "user_responsibility.settings"
 * )
 */
class UserResponsibility extends ContentEntityBase implements UserResponsibilityInterface {

  use EntityChangedTrait;

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setSetting('max_length', 32)
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => -10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['user'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User'))
      ->setSetting('target_type', 'user')
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['responsibility'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Responsibility'))
      ->setSetting('target_type', 'responsibility')
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // TODO security group?
    // TODO effective dates

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
