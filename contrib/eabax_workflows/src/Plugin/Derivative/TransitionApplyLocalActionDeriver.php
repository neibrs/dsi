<?php

namespace Drupal\eabax_workflows\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;

/**
 * Provides local action definitions for all workflow transition.
 */
class TransitionApplyLocalActionDeriver extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [];

    $this->buildDerivativeDefinitions($base_plugin_definition, $base_plugin_definition['target_entity_type_id'], $base_plugin_definition['target_field_name']);

    foreach ($this->derivatives as &$entry) {
      $entry += $base_plugin_definition;
    }

    return $this->derivatives;
  }

  protected function buildDerivativeDefinitions($base_plugin_definition, $entity_type_id, $field_name) {
    $field_definitions = \Drupal::service('entity_field.manager')->getBaseFieldDefinitions($entity_type_id);
    $field_definition = $field_definitions[$field_name];
    if ($field_definition->getType() != 'entity_status') {
      return;
    }

    $workflow_id = $field_definitions[$field_name]->getSetting('workflow');
    $workflow = \Drupal::entityTypeManager()->getStorage('workflow')->load($workflow_id);
    /** @var \Drupal\workflows\WorkflowInterface $workflow */
    $plugin = $workflow->getTypePlugin();
    $plugin_id = $plugin->getPluginId();

    $transitions = $plugin->getTransitions();
    foreach ($transitions as $transition) {
      $transition_id = $transition->id();
      $this->derivatives["$entity_type_id.$plugin_id.$transition_id"] = [
        'title' => $transition->label(),
        'route_name' => "eabax_workflows.apply_transition",
        'route_parameters' => [
          'workflow_type' => $workflow->id(),
          'transition_id' => $transition_id,
          'entity_type' => $entity_type_id,
          'field' => $field_definition->getName(),
        ],
        'cache_tags' => $workflow->getCacheTags(),
      ];
    }
  }

}
