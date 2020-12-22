<?php

/**
 * @file
 */

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlEntityStorageInterface;

/**
 * Implements hook_views_data_alter().
 */
function views_plus_views_data_alter(array &$data) {
  $entity_type_manager = \Drupal::entityTypeManager();
  $entity_types = $entity_type_manager->getDefinitions();
  /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager */
  $entity_field_manager = \Drupal::service('entity_field.manager');
  foreach ($entity_types as $entity_type_id => $entity_type) {
    if ($entity_type instanceof ContentEntityTypeInterface) {
      $storage = $entity_type_manager->getStorage($entity_type_id);
      if ($storage instanceof SqlEntityStorageInterface) {
        $table_mapping = $storage->getTableMapping();
        $table_name = $table_mapping->getDataTable() ?: $table_mapping->getBaseTable();
        $field_storage_definitions = $entity_field_manager->getFieldStorageDefinitions($entity_type_id);
        foreach ($field_storage_definitions as $field_name => $field_storage_definition) {
          if ($field_storage_definition->getType() == 'entity_reference') {
            // 升级实体新字段时，新字段还未在数据表内
            if (!in_array($field_name, $table_mapping->getAllColumns($table_name))) {
              continue;
            }
            $table_name = $table_mapping->getFieldTableName($field_name);
            $column_name = $table_mapping->getFieldColumnName($field_storage_definition, 'target_id');
            if (!isset($data[$table_name][$column_name]['filter'])) {
              continue;
            }
            if (in_array($data[$table_name][$column_name]['filter']['id'], ['numeric', 'string'])) {
              $data[$table_name][$column_name]['filter']['id'] = 'entity_reference_in_operator';
              $data[$table_name][$column_name]['filter']['field_name'] = $field_name;
            }
          }
        }
      }
    }
  }
}

/**
 * Implements hook_field_views_data_alter().
 */
/*function views_plus_field_views_data_alter(array &$data, FieldStorageConfigInterface $field_storage) {
if ($field_storage->getType() == 'entity_reference') {
foreach ($data as $table_name => $table_data) {
foreach ($table_data as $field_name => $field_data) {
if (isset($field_data['filter']) && $field_name != 'delta') {
if (in_array($field_data['filter']['id'], ['numeric', 'string'])) {
$data[$table_name][$field_name]['filter']['id'] = 'entity_reference_in_operator';
}
}
}
}
}
}*/
