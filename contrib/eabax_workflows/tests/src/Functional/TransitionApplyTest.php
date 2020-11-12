<?php

namespace Drupal\Tests\eabax_workflows\Functional;

/**
 * Tests for workflow transition.
 *
 * @group eabax_workflows
 */
class TransitionApplyTest extends EabaxWorkflowsTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block'];

  /**
   * Tests status for transition apply.
   */
  public function testTransitionApply() {
    $this->drupalPlaceBlock('local_actions_block');

    $user = $this->createUser([]);
    $this->drupalLogin($user);
    $assert_session = $this->assertSession();

    // 1. 创建工作流
    $settings = [
      'states' => [
        'draft' => [
          'label' => 'Draft',
          'weight' => 0,
        ],
        'submitted' => [
          'label' => 'Submitted',
          'weight' => 1,
        ],
        'approved' => [
          'label' => 'Approved',
          'weight' => 2,
        ],
        'rejected' => [
          'label' => 'Rejected',
          'weight' => 3,
        ],
        'in_progress' => [
          'label' => 'In Process',
          'weight' => 4,
        ],
        'cancelled' => [
          'label' => 'Cancelled',
          'weight' => 5,
        ],
      ],
      'transitions' => [
        'submit' => [
          'label' => 'Submit',
          'from' => [
            'draft',
            'rejected',
          ],
          'to' => 'submitted',
          'weight' => 0,
        ],
        'approve' => [
          'label' => 'Approve',
          'from' => [
            'submitted',
          ],
          'to' => 'approved',
          'weight' => 0,
        ],
        'reject' => [
          'label' => 'Reject',
          'from' => [
            'submitted',
          ],
          'to' => 'rejected',
          'weight' => 0,
        ],
        'cancel' => [
          'label' => 'Cancel',
          'from' => [
            'draft',
            'submitted',
            'rejected',
          ],
          'to' => 'cancelled',
          'weight' => 0,
        ],
      ],
      'entity_type_id' => 'node',
    ];
    $workflow = $this->createWorkflow($settings);

    $field_name = $this->createEntityStatusField($workflow);
    $node = $this->createNode([
      'type' => $this->contentType->id(),
      $field_name => 'submitted',
    ]);

    $this->drupalGet('node/' . $node->id());
    $assert_session->statusCodeEquals(200);

    $assert_session->linkNotExists(t('Submit'));
    $assert_session->linkExists(t('Approve'));
    $assert_session->linkExists(t('Cancel'));

    $this->drupalGet('node/' . $node->id());
    $this->clickLink(t('Approve'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkNotExists(t('Submit'));
    $assert_session->linkNotExists(t('Reject'));

  }

}
