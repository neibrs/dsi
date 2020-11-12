<?php

namespace Drupal\responsibility\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntity;

/**
 * Defines the Responsibility entity.
 *
 * @ingroup responsibility
 *
 * @ContentEntityType(
 *   id = "responsibility",
 *   label = @Translation("Responsibility"),
 *   label_collection = @Translation("Responsibilities"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\responsibility\ResponsibilityListBuilder",
 *     "views_data" = "Drupal\responsibility\Entity\ResponsibilityViewsData",
 *     "translation" = "Drupal\responsibility\ResponsibilityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\responsibility\Form\ResponsibilityForm",
 *       "add" = "Drupal\responsibility\Form\ResponsibilityForm",
 *       "edit" = "Drupal\responsibility\Form\ResponsibilityForm",
 *       "delete" = "Drupal\responsibility\Form\ResponsibilityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\responsibility\ResponsibilityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\responsibility\ResponsibilityAccessControlHandler",
 *   },
 *   base_table = "responsibility",
 *   data_table = "responsibility_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer responsibilities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/responsibility/{responsibility}",
 *     "add-form" = "/responsibility/add",
 *     "edit-form" = "/responsibility/{responsibility}/edit",
 *     "delete-form" = "/responsibility/{responsibility}/delete",
 *     "collection" = "/responsibility",
 *   },
 *   field_ui_base_route = "responsibility.settings",
 *   multiple_organization_classification = "business_group",
 * )
 */
class Responsibility extends EffectiveDatesBusinessGroupEntity implements ResponsibilityInterface {

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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Responsibility'))
      ->setSetting('max_length', 32)
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setSetting('max_length', 64)
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

    $fields['security_profile'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Security profile'))
      ->setSetting('target_type', 'security_profile')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
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

    return $fields;
  }

}
