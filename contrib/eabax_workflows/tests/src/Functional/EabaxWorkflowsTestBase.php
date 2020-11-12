<?php

namespace Drupal\Tests\eabax_workflows\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\BrowserTestBase;
use Drupal\workflows\Entity\Workflow;
use Drupal\workflows\WorkflowInterface;

class EabaxWorkflowsTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['eabax_workflows', 'node', 'entity_workflows_test'];

  /**
   * @var \Drupal\node\NodeTypeInterface
   */
  protected $contentType;

  protected function setUp() {
    parent::setUp();

    $this->contentType = $this->drupalCreateContentType([
      'name' => $this->randomMachineName(),
      'type' => strtolower($this->randomMachineName()),
    ]);
  }

  protected function createEntityStatusField(WorkflowInterface $workflow) {
    $field_name = strtolower($this->randomMachineName());
    FieldStorageConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'node',
      'type' => 'entity_status',
      'settings' => [
        'workflow_type' => $workflow->getTypePlugin()->getPluginId(),
        'workflow' => $workflow->id(),
      ],
    ])->save();
    FieldConfig::create([
      'field_name' => $field_name,
      'entity_type' => 'node',
      'bundle' => $this->contentType->id(),
    ])->save();

    return $field_name;
  }

  /**
   * @return \Drupal\workflows\Entity\Workflow
   */
  protected function createWorkflow(array $settings = []) {
    $settings += [
      'id' => strtolower($this->randomMachineName()),
      'type' => 'entity_workflows_test',
      'entity_type_id' => 'node',
      'states' => [],
      'transitions' => [],
    ];

    $workflow = Workflow::create($settings);
    $workflow->save();

    return $workflow;
  }

}
