<?php

namespace Drupal\Tests\eabax_workflows\Functional;

/**
 * Tests for status control.
 *
 * @group eabax_workflows
 */
class StatusControlTest extends EabaxWorkflowsTestBase {

  /**
   * Tests status control.
   */
  public function testStatusControl() {
    $user = $this->drupalCreateUser([]);
    $this->drupalLogin($user);
    $assert_session = $this->assertSession();

    $settings = [
      'states' => [
        'state1' => [
          'id' => 'state1',
          'label' => 'state1',
          'fields_control' => [
            'promote' => [
              'can_view' => TRUE,
              'can_update' => TRUE,
              'value' => FALSE,
              'status_setting' => 'default_value',
            ],
          ],
        ],
        'state2' => [
          'id' => 'state2',
          'label' => 'state2',
          'fields_control' => [
            'promote' => [
              'can_view' => TRUE,
              'can_update' => FALSE,
              'value' => TRUE,
              'status_setting' => 'sets_value',
            ],
          ],
        ],
      ],
    ];
    $workflow = $this->createWorkflow($settings);

    $field_name = $this->createEntityStatusField($workflow);
    $node = $this->createNode([
      'type' => $this->contentType->id(),
      $field_name => 'state1',
    ]);
    $assert_session->assert($node->isPromoted(), 'value is TRUE');

    $node->$field_name->value = 'state2';
    $node->save();
    $assert_session->assert($node->isPromoted(), 'sets value to TRUE');
    $node->setPromoted(FALSE);
    $assert_session->assert(!$node->isPromoted(), 'value is FALSE');
  }

}
