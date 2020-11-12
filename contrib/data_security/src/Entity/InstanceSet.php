<?php

namespace Drupal\data_security\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntity;

/**
 * Defines the Instance set entity.
 *
 * @ingroup data_security
 *
 * @ContentEntityType(
 *   id = "instance_set",
 *   label = @Translation("Instance set"),
 *   label_collection = @Translation("Instance sets"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\data_security\InstanceSetListBuilder",
 *     "views_data" = "Drupal\data_security\Entity\InstanceSetViewsData",
 *     "translation" = "Drupal\data_security\InstanceSetTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\data_security\Form\InstanceSetForm",
 *       "add" = "Drupal\data_security\Form\InstanceSetForm",
 *       "edit" = "Drupal\data_security\Form\InstanceSetForm",
 *       "delete" = "Drupal\data_security\Form\InstanceSetDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\data_security\InstanceSetHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\data_security\InstanceSetAccessControlHandler",
 *   },
 *   base_table = "instance_set",
 *   data_table = "instance_set_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer instance set entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/instance_set/{instance_set}",
 *     "add-form" = "/instance_set/add",
 *     "edit-form" = "/instance_set/{instance_set}/edit",
 *     "delete-form" = "/instance_set/{instance_set}/delete",
 *     "collection" = "/instance_set",
 *   },
 *   field_ui_base_route = "instance_set.settings",
 *   match_fields = {"name", "code", "description", "pinyin"},
 * )
 */
class InstanceSet extends EffectiveDatesBusinessGroupEntity implements InstanceSetInterface {

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

    $fields['entity_type'] = BaseFieldDefinition::create('entity_type')
      ->setLabel(t('Object'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 32)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'entity_type',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_type_select',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 32)
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

    $fields['code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Code'))
      ->setSetting('max_length', 32)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setSetting('max_length', 32)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['pinyin'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Pinyin shortcode'))
      ->setSetting('max_length', 64);

    $fields['predicate'] = BaseFieldDefinition::create('entity_filter')
      ->setLabel(t('Predicate'))
      ->setSetting('target_type_field', 'entity_type')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'entity_filter',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_filter',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function withinScope($entity_id) {
    $entity_type = $this->entityTypeManager()->getDefinition($this->entity_type->value);
    $base_table = $entity_type->getDataTable() ?: $entity_type->getBaseTable();
    $view = \Drupal::service('entity_filter.manager')->createView($base_table, ['filters' => $this->predicate->value]);
    $view->build('default');
    $query = $view->build_info['query'];

    $query->condition($base_table . '.id', $entity_id);

    $query->preExecute();
    $ids = $query->execute()->fetchcol();
    return !empty($ids);
  }

}
