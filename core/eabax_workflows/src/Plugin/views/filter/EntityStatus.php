<?php

namespace Drupal\eabax_workflows\Plugin\views\filter;

use Drupal\views\FieldAPIHandlerTrait;
use Drupal\views\Plugin\views\filter\InOperator;
use Drupal\workflows\Entity\Workflow;

/**
 * @ViewsFilter("entity_status")
 */
class EntityStatus extends InOperator {

  use FieldAPIHandlerTrait;

  /**
   * {@inheritdoc}
   */
  public function getValueOptions() {
    if (!isset($this->valueOptions)) {
      $field_storage = $this->getFieldStorageDefinition();
      $workflow = Workflow::load($field_storage->getSetting('workflow'));
      $states = $workflow->getTypePlugin()->getStates();
      $this->valueOptions = array_map(function ($state) {
        return $state->label();
      }, $states);
    }
    return $this->valueOptions;
  }

}
