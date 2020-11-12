<?php

namespace Drupal\dsi_import\Plugin\migrate\destination;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\migrate\Plugin\migrate\destination\EntityContentBase as EntityContentBaseBase;
use Drupal\migrate\Row;
use Drupal\dsi_import\ImportTrait;

/**
 * Import user added field automatically.
 */
class EntityContentBase extends EntityContentBaseBase {

  use ImportTrait;

  /**
   * @see \Drupal\migrate\Plugin\migrate\destination\Entity::getEntity()
   */
  protected function getEntity(Row $row, array $old_destination_id_values) {
    // 采用从数据库中获得 entity_id，不采用以前导入是 migration_map 记录的 entity_id.
    $entity_id = $this->getEntityId($row);
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
  protected function getEntityId(Row $row) {
    if ($entity_id = parent::getEntityId($row)) {
      return $entity_id;
    }

    $destination_configuration = $this->migration->getDestinationConfiguration();
    if (isset($destination_configuration['keys'])) {
      $properties = [];
      foreach ($destination_configuration['keys'] as $keys) {
        if (is_array($keys)) {
          $found_all = TRUE;
          foreach ($keys as $key) {
            if ($value = $row->getDestinationProperty(str_replace('__', Row::PROPERTY_SEPARATOR, $key))) {
              $properties[$key] = $value;
            }
            else {
              $found_all = FALSE;
              break;
            }
          }
          // 要求所有的 keys 必须有，否则不处理.
          if (!$found_all) {
            continue;
          }
        }
        else {
          $value = $row->getDestinationProperty($keys);
          // 值不为空才处理.
          if (!empty($value)) {
            $properties[$keys] = $value;
          }
          else {
            continue;
          }
        }

        // Todo Add multiple organization query
        $entities = $this->storage->loadByProperties($properties);
        if (!empty($entities)) {
          $entity = reset($entities);
          $entity_id = $entity->id();
          break;
        }
        else {
          $properties = [];
        }
      }
    }

    return $entity_id;
  }

  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    $entity_type_id = $this->storage->getEntityTypeId();

    // 为 getFieldDefinitions 函数准备 bundle 参数.
    $entity_id = $this->getEntityId($row);
    if (!empty($entity_id) && ($entity = $this->storage->load($entity_id))) {
      $bundle = $entity->bundle();
    }
    else {
      $bundle = $this->getBundle($row);
      if (empty($bundle)) {
        $bundle = $row->getSourceProperty($this->getKey('bundle')) ?: $entity_type_id;
      }
    }

    /** @var \Drupal\Core\Field\FieldDefinitionInterface[] $field_definitions */
    $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type_id, $bundle);

    foreach ($field_definitions as $field_name => $field_definition) {
      // 只通过 migration 配置处理非基础字段，不自动匹配基础字段.
      if ($field_definition instanceof BaseFieldDefinition) {
        continue;
      }

      // 已经通过 migration 配置处理好的字段，不再处理.
      if ($row->hasDestinationProperty($field_name)) {
        continue;
      }

      $label = $field_definition->getLabel();
      if (!is_string($label)) {
        $label = $label->render();
      }

      $value = $row->getSourceProperty($label);
      if (empty($value)) {
        continue;
      }

      // 判断字段类型进行细致的处理.
      switch ($field_definition->getType()) {
        case 'entity_reference':
          $selection_handler = \Drupal::service('plugin.manager.entity_reference_selection')->getSelectionHandler($field_definition);
          $entities = $selection_handler->getReferenceableEntities($value, '=');
          if ($entity = reset($entities)) {
            $row->setDestinationProperty($field_name, array_search($value, $entity));
          }
          else {
            \Drupal::messenger()->addWarning($this->t('@entity does not found: @label', [
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
            \Drupal::messenger()->addWarning($this->t('The allowed options for @field are: @allowed_options. @value is not allowed.', [
              '@field' => $field_definition->getLabel(),
              '@allowed_options' => implode(',', array_values($allowed_options)),
              '@value' => $value,
            ]));
          }
          break;

        case 'datetime':
          // 检查日期格式.
          if (preg_match('/[\(\)(\x80-\xff)]+/', $value) || (strpos($value, '-') && strpos($value, '/')) ||
            (strpos($value, '-') && strpos($value, '.')) || (strpos($value, '.') && strpos($value, '/'))) {
            $this->messenger()->addWarning($this->t('@value : Date format is incorrect.', [
              '@value' => $value,
            ]));
            break;
          }

          // 日期格式处理
          if (preg_match('/^[\d]+[\-\/.]*/', $value)) {

            if (strpos($value, '.')) {
              $value = str_replace('.', '-', $value);
            }
            if (strpos($value, '/')) {
              $value = str_replace('/', '-', $value);
            }

            // 正常的时间处理
            if (strtotime($value)) {
              $value = date('Y-m-d', strtotime($value));
            }
            else {
              // 如果是 excel 的时间，则另外处理
              $value = gmdate('Y-m-d', ($value - 25569) * 86400);
            }
          }
          // 字符串格式处理
          else {
            $value = date('Y-m-d', strtotime($value));
          }
          $row->setDestinationProperty($field_name, $value);
          break;

        default:
          $row->setDestinationProperty($field_name, $value);
          break;
      }
    }

    // 根据 default_value_fields 设置默认值.
    $default_value_fields = \Drupal::config('import.settings')->get('default_value_fields');
    if (isset($default_value_fields[$entity_type_id])) {
      foreach ($default_value_fields[$entity_type_id] as $field_name) {
        $value = $row->getDestinationProperty($field_name);
        if (empty($value) && !empty($row->getSourceProperty($field_name))) {
          $row->setDestinationProperty($field_name, $row->getSourceProperty($field_name));
        }
      }
    }

    // parent::import 的 $this->getEntity 会根据核心处理过程的 $row->emptyDestinationProperties 删除字段数据
    // 需要删除 $row->emptyDestinationProperties 中的 $default_value_fields
    $row_class = new \ReflectionClass(get_class($row));
    $empty_field = $row_class->getProperty('emptyDestinationProperties');
    $empty_field->setAccessible(TRUE);
    $empty_destination_properties = $empty_field->getValue($row);
    $empty_destination_properties = array_diff($empty_destination_properties, $default_value_fields[$entity_type_id] ?: []);
    $empty_field->setValue($row, $empty_destination_properties);

    $ids = parent::import($row, $old_destination_id_values);
    return $ids;
  }

}
