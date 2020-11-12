<?php

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\Sql\SqlEntityStorageInterface;

/**
 * Implements hook_views_data_alter().
 */
function person_views_data_alter(array &$data) {
  $entity_type_manager = \Drupal::entityTypeManager();
  $definitions = $entity_type_manager->getDefinitions();
  foreach ($definitions as $entity_type_id => $definition) {
    if ($entity_type_id == 'person') {
      $debug = 1;
    }
    if ($definition instanceof ContentEntityTypeInterface) {
      $storage = $entity_type_manager->getStorage($entity_type_id);
      if ($storage instanceof SqlEntityStorageInterface) {
        $table_mapping = $storage->getTableMapping();
        $field_definitions = \Drupal::service('entity_field.manager')->getFieldStorageDefinitions($entity_type_id);
        foreach ($field_definitions as $field_name => $field_definition) {
          if ($field_definition->getType() == 'entity_reference' && in_array($field_definition->getSetting('target_type'), ['person', 'contract', 'employee_assignment', 'person_address', 'person_email', 'person_phone', 'identification_information', 'administrative_area'])) {
            $table_name = $table_mapping->getFieldTableName($field_name);
            $column_name = $table_mapping->getFieldColumnName($field_definition, 'target_id');
            if (isset($data[$table_name][$column_name]['filter'])) {
              /** @see views_plus_views_data_alter() */
              //$data[$table_name][$column_name]['filter']['id'] = 'entity_reference_in_operator';
              //$data[$table_name][$column_name]['filter']['field_name'] = $field_name;
              $data[$table_name][$column_name]['filter']['widget_type'] = 'entity_autocomplete';
            }
          }
        }
      }
    }
  }

}
