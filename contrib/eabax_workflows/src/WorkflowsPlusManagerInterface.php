<?php

namespace Drupal\eabax_workflows;

use Drupal\Core\Entity\EntityInterface;

interface WorkflowsPlusManagerInterface {

  public function getAllWorkflowFields();

  public function getWorkflowFields($workflow_id);
  
  public function getEntityTypeWorkflowFields($entity_type_id);

  public function applyTransition(EntityInterface $entity, $field_id, $transition_id);

}