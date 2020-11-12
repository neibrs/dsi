<?php

namespace Drupal\data_security\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntity;

/**
 * Defines the Data security entity.
 *
 * @ingroup data_security
 *
 * @ContentEntityType(
 *   id = "data_security",
 *   label = @Translation("Data security policy"),
 *   label_collection = @Translation("Data security policies"),
 *   bundle_label = @Translation("Data scope"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\data_security\DataSecurityListBuilder",
 *     "views_data" = "Drupal\data_security\Entity\DataSecurityViewsData",
 *     "translation" = "Drupal\data_security\DataSecurityTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\data_security\Form\DataSecurityForm",
 *       "add" = "Drupal\data_security\Form\DataSecurityForm",
 *       "edit" = "Drupal\data_security\Form\DataSecurityForm",
 *       "delete" = "Drupal\data_security\Form\DataSecurityDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\data_security\DataSecurityHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\data_security\DataSecurityAccessControlHandler",
 *   },
 *   base_table = "data_security",
 *   data_table = "data_security_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer data securities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/data_security/{data_security}",
 *     "add-page" = "/data_security/add",
 *     "add-form" = "/data_security/add/{data_security_type}",
 *     "edit-form" = "/data_security/{data_security}/edit",
 *     "delete-form" = "/data_security/{data_security}/delete",
 *     "collection" = "/data_security",
 *   },
 *   bundle_entity_type = "data_security_type",
 *   field_ui_base_route = "entity.data_security_type.edit_form",
 *   match_fields = {"name", "pinyin"},
 * )
 */
class DataSecurity extends EffectiveDatesBusinessGroupEntity implements DataSecurityInterface {

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
      ->setLabel(t('Name'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 32)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['pinyin'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Pinyin shortcode'))
      ->setSetting('max_length', 64);

    $fields['entity_type'] = BaseFieldDefinition::create('entity_type')
      ->setLabel(t('Object'))
      ->setRequired(TRUE)
      ->setSetting('is_ascii', TRUE)
      ->setSetting('max_length', EntityTypeInterface::ID_MAX_LENGTH)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'entity_type',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_type_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['entity_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Entity ID'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public static function bundleFieldDefinitions(EntityTypeInterface $entity_type, $bundle, array $base_field_definitions) {
    $fields = parent::bundleFieldDefinitions($entity_type, $bundle, $base_field_definitions);

    switch ($bundle) {
      case 'all_rows':
        break;
      case 'instance':
        $fields['entity_id'] = clone $base_field_definitions['entity_id'];
        $fields['entity_id']
          ->setLabel(t('Instance'))
          ->setRequired(TRUE)
          ->setDisplayOptions('view', [
            'label' => 'inline',
            'type' => 'entity_reference_label',
            'weight' => 0,
          ])
          ->setDisplayOptions('form', [
            'type' => 'entity_reference_autocomplete',
            'weight' => 0,
          ])
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);
        break;
      case 'instance_set':
        $fields['entity_id'] = clone $base_field_definitions['entity_id'];
        $fields['entity_id']
          ->setLabel(t('Instance set'))
          ->setRequired(TRUE)
          ->setSetting('target_type', 'instance_set')
          ->setDisplayOptions('view', [
            'label' => 'inline',
            'type' => 'entity_reference_label',
            'weight' => 0,
          ])
          ->setDisplayOptions('form', [
            'type' => 'entity_reference_autocomplete',
            'weight' => 0,
          ])
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);
        break;
    }

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function withinScope($entity_id) {
    switch ($this->type->target_id) {
      case 'all_row':
        return TRUE;
      case 'instance':
        return $entity_id == $this->entity_id->target_id;
      case 'instance_set':
        return $this->entity_id->entity->withinScope($entity_id);
    }
  }

}
