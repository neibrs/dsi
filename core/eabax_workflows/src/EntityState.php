<?php

namespace Drupal\eabax_workflows;

use Drupal\workflows\State;

class EntityState extends State implements EntityStateInterface {

  protected $entityControl;

  protected $fieldsControl;

  /**
   * {@inheritdoc}
   */
  public function __construct($workflow, $id, $label, $weight, $entity_control, $fields_control) {
    parent::__construct($workflow, $id, $label, $weight);

    $this->entityControl = $entity_control;
    $this->fieldsControl = $fields_control;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityControl() {
    return $this->entityControl;
  }

  /**
   * {@inheritdoc}
   */
  public function getFieldsControl() {
    return $this->fieldsControl;
  }

  /**
   * {@inheritdoc}
   */
  public function getCanView($field, $default = TRUE) {
    if (isset($this->fieldsControl[$field]['view'])) {
      return $this->fieldsControl[$field]['view'];
    }
    else {
      return $default;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCanUpdate($field, $default) {
    if (isset($this->fieldsControl[$field]['edit'])) {
      return $this->fieldsControl[$field]['edit'];
    }
    else {
      return $default;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getStatusSettingValue($field) {
    if (isset($this->fieldsControl[$field]['value'])) {
      return $this->fieldsControl[$field]['value'];
    }
    else {
      $configuration = $this->workflow->getConfiguration();
      if (isset($configuration['entity_type_id']) && !empty($configuration['entity_type_id'])) {
        $field_definition = \Drupal::entityManager()->getFieldStorageDefinitions($configuration['entity_type_id'])[$field];
        $default = $field_definition->getDefaultValueLiteral();
        if (!empty($default)) {
          $default = $default[0]['value'];
          return $default;
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getStatusSetting($field, $default = 'not_used') {
    if (isset($this->fieldsControl[$field]['status_setting'])) {
      return $this->fieldsControl[$field]['status_setting'];
    }
    else {
      return $default;
    }
  }

}
