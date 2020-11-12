<?php

namespace Drupal\entity_workflows_test\Plugin\WorkflowType;

use Drupal\eabax_workflows\Plugin\WorkflowType\EntityWorkflowBase;

/**
 * Test workflow type.
 *
 * @WorkflowType(
 *   id = "entity_workflows_test",
 *   label = @Translation("Entity Workflow Test"),
 * )
 */
class TestEntityWorkflow extends EntityWorkflowBase {

}
