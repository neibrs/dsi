<?php

namespace Drupal\eabax_workflows;

use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

class WorkflowsPlusManager implements WorkflowsPlusManagerInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Array of fields keyed by workflow ID and entity type ID.
   *
   * @var array
   */
  protected $workflowFields = [];

  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getAllWorkflowFields() {
    if (empty($this->workflowFields)) {
      $entity_types = $this->entityTypeManager->getDefinitions();
      foreach ($entity_types as $entity_type) {
        if ($entity_type instanceof ContentEntityTypeInterface) {
          $fields = $this->entityFieldManager->getBaseFieldDefinitions($entity_type->id());
          foreach ($fields as $field) {
            if ($field->getType() == 'entity_status') {
              $this->workflowFields[$field->getSetting('workflow')][$entity_type->id()] = $field;
              break;
            }
          }
        }
      }
    }

    return $this->workflowFields;
  }

  /**
   * {@inheritdoc}
   */
  public function getWorkflowFields($workflow_id) {
    $workflow_fields = $this->getAllWorkflowFields();
    if (isset($workflow_fields[$workflow_id])) {
      return $workflow_fields[$workflow_id];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function applyTransition(EntityInterface $entity, $field_id, $transition_id) {
    $field_definitions = $this->entityFieldManager->getFieldDefinitions($entity->getEntityTypeId(), $entity->bundle());
    $workflow_id = $field_definitions[$field_id]->getSetting('workflow');

    /** @var \Drupal\workflows\WorkflowInterface $workflow */
    $workflow = $this->entityTypeManager->getStorage('workflow')->load($workflow_id);
    $transition = $workflow->getTypePlugin()->getTransition($transition_id);
    $from = $transition->from();
    $from = array_map(function ($item) {
      /** @var \Drupal\workflows\StateInterface $item */
      return $item->id();
    }, $from);
    if (in_array($entity->get($field_id)->value, $from)) {
      $entity->set($field_id, $transition->to()->id());
      $entity->save();
    }
  }

}
