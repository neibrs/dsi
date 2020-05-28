<?php

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Implements hook_views_data_alter().
 */
function eabax_workflows_views_data_alter(array &$data) {
  $entity_type_manager = \Drupal::entityTypeManager();
  $entity_types = $entity_type_manager->getDefinitions();
  foreach ($entity_types as $entity_type) {
    if ($entity_type instanceof ContentEntityTypeInterface) {
      $field_definitions = \Drupal::entityManager()
        ->getBaseFieldDefinitions($entity_type->id());
      $storage = $entity_type_manager->getStorage($entity_type->id());
      if (!($storage instanceof SqlContentEntityStorage)) {
        continue;
      }
      /** @var \Drupal\Core\Entity\Sql\DefaultTableMapping $table_mapping */
      if ($table_mapping = $storage->getTableMapping($field_definitions)) {
        foreach ($table_mapping->getTableNames() as $table) {
          foreach ($table_mapping->getFieldNames($table) as $field_name) {
            if (isset($data[$table][$field_name]['filter'])) {
              if ($field_definitions[$field_name]->getType() == 'entity_status' && $data[$table][$field_name]['filter']['id'] == 'string') {
                $data[$table][$field_name]['filter']['id'] = 'entity_status';
                $data[$table][$field_name]['filter']['field_name'] = $field_name;
              }
            }
          }
        }
      }
    }
  }
}
