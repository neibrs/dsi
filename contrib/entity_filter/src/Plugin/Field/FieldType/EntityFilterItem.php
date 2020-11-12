<?php

namespace Drupal\entity_filter\Plugin\Field\FieldType;

use Drupal\Core\Field\Plugin\Field\FieldType\MapItem;

/**
 * Defines the 'entity_filter' entity field type.
 *
 * @FieldType(
 *   id = "entity_filter",
 *   label = @Translation("Entity filter"),
 *   description = @Translation("An entity field for storing a entity filters array."),
 *   default_widget = "entity_filter",
 *   default_formatter = "entity_filter",
 * )
 */
class EntityFilterItem extends MapItem implements EntityFilterItemInterface {

  /**
   * {@inheritdoc}
   */
  public static function defaultStorageSettings() {
    return [
      'target_type' => '',
      'target_type_field' => '',
    ] + parent::defaultStorageSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function getTargetType() {
    if (!empty($target_type = $this->getSetting('target_type'))) {
      return $target_type;
    }
    if (!empty($target_type_field = $this->getSetting('target_type_field'))) {
      if ($entity = $this->getEntity()) {
        return $entity->$target_type_field->value;
      }
    }
  }

}
