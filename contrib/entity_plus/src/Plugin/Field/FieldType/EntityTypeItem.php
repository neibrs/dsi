<?php

namespace Drupal\entity_plus\Plugin\Field\FieldType;

use Drupal\Core\Field\Plugin\Field\FieldType\StringItem;

/**
 * Defines the 'entity_type' entity field type.
 *
 * @FieldType(
 *   id = "entity_type",
 *   label = @Translation("Entity type"),
 *   category = @Translation("Text"),
 *   default_widget = "entity_type_select",
 *   default_formatter = "entity_type"
 * )
 */
class EntityTypeItem extends StringItem {

}
