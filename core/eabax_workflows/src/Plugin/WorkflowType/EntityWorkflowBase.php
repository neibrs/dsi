<?php

namespace Drupal\eabax_workflows\Plugin\WorkflowType;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\eabax_workflows\EntityState;
use Drupal\eabax_workflows\EntityTransition;
use Drupal\workflows\Plugin\WorkflowTypeBase;

/**
 * Only allow the workflow be used by specified entity_type_id.
 */
abstract class EntityWorkflowBase extends WorkflowTypeBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'states' => [],
      'transitions' => [],
      'entity_type_id' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getState($state_id) {
    if (!isset($this->configuration['states'][$state_id])) {
      throw new \InvalidArgumentException("The state '$state_id' does not exist in workflow.");
    }
    return new EntityState(
      $this,
      $state_id,
      $this->configuration['states'][$state_id]['label'],
      $this->configuration['states'][$state_id]['weight'],
      isset($this->configuration['states'][$state_id]['entity_control']) ? $this->configuration['states'][$state_id]['entity_control'] : [],
      isset($this->configuration['states'][$state_id]['fields_control']) ? $this->configuration['states'][$state_id]['fields_control'] : []
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getTransition($transition_id) {
    if (!$this->hasTransition($transition_id)) {
      throw new \InvalidArgumentException("The transition '$transition_id' does not exist in workflow.");
    }
    return new EntityTransition(
      $this,
      $transition_id,
      $this->configuration['transitions'][$transition_id]['label'],
      $this->configuration['transitions'][$transition_id]['from'],
      $this->configuration['transitions'][$transition_id]['to'],
      $this->configuration['transitions'][$transition_id]['weight'],
      isset($this->configuration['transitions'][$transition_id]['conditions']) ? $this->configuration['transitions'][$transition_id]['conditions'] : []
    );
  }

  /**
   * @return \Drupal\workflows\StateInterface
   */
  public function getEntityState(ContentEntityInterface $entity) {
    if ($entity->hasField('status')) {
      $field = $entity->getFieldDefinition('status');
      /** @var \Drupal\workflows\WorkflowInterface $workflow */
      $workflow = \Drupal::entityTypeManager()->getStorage('workflow')->load($field->getSetting('workflow'));
      if ($field->getType() == 'entity_status' && $workflow->getTypePlugin()->getPluginId() == $this->getPluginId()) {
        $state = $entity->get($field->getName())->value;
        return $this->getState($state);
      }
    }
  }

}
