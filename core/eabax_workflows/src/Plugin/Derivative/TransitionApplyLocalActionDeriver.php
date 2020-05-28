<?php

namespace Drupal\eabax_workflows\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\eabax_workflows\Plugin\WorkflowType\EntityWorkflowBase;

/**
 * Provides local action definitions for all workflow transition.
 */
class TransitionApplyLocalActionDeriver extends DeriverBase {

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $this->derivatives = [];

    $workflow_storage = \Drupal::entityTypeManager()->getStorage('workflow');

    $all_fields = \Drupal::service('workflows_plus.manager')->getAllWorkflowFields();
    foreach ($all_fields as $workflow_id => $fields) {
      /** @var \Drupal\workflows\WorkflowInterface $workflow */
      $workflow = $workflow_storage->load($workflow_id);
      if (!$workflow) {
        trigger_error('The workflow config "' . $workflow_id . '" not found.');
        continue;
      }

      /** @var \Drupal\eabax_workflows\Plugin\WorkflowType\EntityWorkflowBase $plugin */
      $plugin = $workflow->getTypePlugin();
      if (!$plugin instanceof EntityWorkflowBase) {
        continue;
      }

      /** @var \Drupal\Core\Field\FieldDefinitionInterface[] $fields */
      foreach ($fields as $entity_type_id => $field) {
        $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);

        $appears_on = [];
        if ($entity_type->hasLinkTemplate('edit-form')) {
          $appears_on[] = "entity.$entity_type_id.edit_form";
        }
        if ($entity_type->hasLinkTemplate('canonical')) {
          $appears_on[] = "entity.$entity_type_id.canonical";
        }
        if (empty($appears_on)) {
          continue;
        }

        $plugin_id = $plugin->getPluginId();
        $transitions = $plugin->getTransitions();
        foreach ($transitions as $transition) {
          $transition_id = $transition->id();
          $this->derivatives["$entity_type_id.$plugin_id.$transition_id"] = [
            'route_name' => "eabax_workflows.apply_transition",
            'route_parameters' => [
              'workflow_type' => $workflow->id(),
              'transition_id' => $transition_id,
              'entity_type' => $entity_type_id,
              'field' => $field->getName(),
            ],
            'title' => $transition->label(),
            'appears_on' => $appears_on,
          ];
        }
      }
    }

    foreach ($this->derivatives as &$entry) {
      $entry += $base_plugin_definition;
    }

    return $this->derivatives;
  }

}
