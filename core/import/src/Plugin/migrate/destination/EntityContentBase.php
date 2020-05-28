<?php

namespace Drupal\import\Plugin\migrate\destination;

use Drupal\migrate\MigrateException;
use Drupal\migrate\Plugin\migrate\destination\EntityContentBase as EntityContentBaseBase;
use Drupal\migrate\Row;

/**
 * Import user added field automatically.
 */
class EntityContentBase extends EntityContentBaseBase {

  /**
   * {@inheritdoc}
   */
  protected function getEntity(Row $row, array $old_destination_id_values) {
    $entity_id = reset($old_destination_id_values) ?: $this->getEntityId($row);
    if (empty($entity_id)) {
      $destination_configuration = $this->migration->getDestinationConfiguration();
      if (isset($destination_configuration['keys'])) {
        $properties = [];
        foreach ($destination_configuration['keys'] as $key) {
          if (!$row->getDestination()[$key]) {
            throw new MigrateException(sprintf('Destination key %s not exist,', $key));
          }
          $properties[$key] = $row->getDestination()[$key];
        }
        // Todo Add multiple organization query
        $entities = $this->storage->loadByProperties($properties);
        if (!empty($entities)) {
          $entity = reset($entities);
          $entity_id = $entity->id();
        }
      }
    }

    if (!empty($entity_id) && ($entity = $this->storage->load($entity_id))) {
      // Allow updateEntity() to change the entity.
      $entity = $this->updateEntity($entity, $row) ?: $entity;
    }
    else {
      // Attempt to ensure we always have a bundle.
      if ($bundle = $this->getBundle($row)) {
        $row->setDestinationProperty($this->getKey('bundle'), $bundle);
      }

      // Stubs might need some required fields filled in.
      if ($row->isStub()) {
        $this->processStubRow($row);
      }
      $entity = $this->storage->create($row->getDestination());
      $entity->enforceIsNew();
    }
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    $entity_type_id = $this->storage->getEntityTypeId();

    // Get bundle.
    $entity_id = $this->getEntityId($row);
    if (!empty($entity_id) && ($entity = $this->storage->load($entity_id))) {
      $bundle = $entity->bundle();
    }
    else {
      $bundle = $this->getBundle($row);
      if (empty($bundle)) {
        $bundle = $entity_type_id;
      }
    }

    /** @var \Drupal\Core\Field\FieldDefinitionInterface[] $field_definitions */
    $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type_id, $bundle);
    foreach ($field_definitions as $field_name => $field_definition) {
      if ($row->hasDestinationProperty($field_name)) {
        continue;
      }

      if ($value =$row->getSourceProperty($field_name)) {
        $row->setDestinationProperty($field_name, $value);
      }
      elseif (($value = $row->getSourceProperty($field_definition->getLabel())) && $field_definition instanceof \Drupal\field\FieldConfigInterface) {
        $value = trim($value);
        if (!empty($value)) {
          // 判断字段类型进行细致的处理.
          switch ($field_definition->getType()) {
            case 'entity_reference':
              $selection_handler = \Drupal::service('plugin.manager.entity_reference_selection')->getSelectionHandler($field_definition);
              $entities = $selection_handler->getReferenceableEntities($value, '=');
              if ($entity = reset($entities)) {
                $row->setDestinationProperty($field_name, array_search($value, $entity));
              }
              else {
                throw new MigrateException($this->t('@entity does not found: @label', [
                  '@entity' => $field_definition->getLabel(),
                  '@label' => $value,
                ]));
              }
              break;
            case 'list_float':
            case 'list_integer':
            case 'list_string':
              $allowed_options = options_allowed_values($field_definition->getFieldStorageDefinition());
              if ($key = array_search($value, $allowed_options)) {
                $row->setDestinationProperty($field_name, $key);
              }
              else {
                throw new MigrateException($this->t('The allowed options for @field are: @allowed_options. @value is not allowed.', [
                  '@field' => $field_definition->getLabel(),
                  '@allowed_options' => implode(',', array_values($allowed_options)),
                  '@value' => $value,
                ]));
              }
              break;
            case 'datetime':
              // 日期格式处理
              if (preg_match('/^[\d.]*$/', $value)) {
                // 正常的时间处理
                if (strtotime($value)) {
                  $value = date('Y-m-d', strtotime($value));
                } else {
                  // 如果是 excel 的时间，则另外处理
                  $value = gmdate('Y-m-d', ($value - 25569) * 86400);
                }
              }
              // 字符串格式处理
              else {
                $value = date('Y-m-d',strtotime($value));
              }
              $row->setDestinationProperty($field_name, $value);
              break;
            default:
              $row->setDestinationProperty($field_name, $value);
              break;
          }
        }
      }
    }
    // Default value fields.
    $default_value_fields = \Drupal::config('import.settings')->get('default_value_fields');
    if (isset($default_value_fields[$entity_type_id])) {
      foreach ($default_value_fields[$entity_type_id] as $field_name) {
        $value = $row->getDestinationProperty($field_name);
        if (empty($value) && !empty($row->getSourceProperty($field_name))) {
          $row->setDestinationProperty($field_name, $row->getSourceProperty($field_name));
        }
      }
    }

    // Imported empty fields do not overwrite existing
    if($row->getSourceProperty('not_override')) {
      $row_class = new \ReflectionClass(get_class($row));
      $empty_field = $row_class->getProperty('emptyDestinationProperties');
      $empty_field->setAccessible(true);
      $empty_field->setValue($row,[]);
    }

    $ids = parent::import($row,$old_destination_id_values);
    return $ids;
  }

}
