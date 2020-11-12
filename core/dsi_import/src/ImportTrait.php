<?php

namespace Drupal\dsi_import;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\field\Entity\FieldConfig;

trait ImportTrait {

  /**
   * Get columns form migration configuration
   */
  protected function getColumns() {
    $columns = [];

    foreach ($this->migration->getSourceConfiguration()['columns'] as $values) {
      $columns = $values + $columns;
    }

    $columns = array_reverse($columns);

    return array_flip($columns);
  }

  protected function getFieldsColumns($fields, array $process) {
    $columns = [];
    $field_names = [];
    foreach ($process as $key => $value) {
      $field_names[$key] = $key;
    }
    foreach ($fields as $id => $field_definition) {
      if (in_array($id, ['id', 'uuid', 'langcode',
        'created', 'changed', 'status',
      ])) {
        continue;
      }
      $label = $field_definition->getLabel();
      if (!is_string($label)) {
        $label = $label->render();
      }

      if (!in_array($id, $field_names)) {
        $columns[$label] = $label;
      }
    }

    return $columns;
  }

  /**
   * 获取自定义字段.
   */
  protected function getFieldConfigColumns($entity_type_id, $bundle) {
    $columns = [];

    $entity_type_definition = \Drupal::entityTypeManager()->getDefinition($entity_type_id);
    if ($entity_type_definition instanceof ContentEntityTypeInterface) {
      $fields = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type_id, $bundle);
      foreach ($fields as $id => $field_definition) {
        if ($field_definition instanceof FieldConfig) {
          $label = $field_definition->getLabel();
          if (!is_string($label)) {
            $label = $label->render();
          }

          $columns[$label] = $label;
        }
      }
    }

    return $columns;
  }

}
