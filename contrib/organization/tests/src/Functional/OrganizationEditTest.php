<?php

namespace Drupal\Tests\organization\Functional;

use Drupal\Core\Url;
use Drupal\Tests\employee_assignment\Traits\EmployeeAssignmentTestTrait;
use Drupal\Tests\location\Traits\LocationTestTraits;

/**
 * Simple test for organization add.
 *
 * @group organization
 */
class OrganizationEditTest extends OrganizationTestBase {

  use LocationTestTraits;

  // 通过 employee_assignment 获得用户所在业务组.
  use EmployeeAssignmentTestTrait {
    setUp as employeeAssignmentSetup;
  }

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block', 'employee_assignment'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->employeeAssignmentSetup();
  }

  /**
   * Tests organization add.
   */
  public function testEdit() {
    $this->drupalPlaceBlock('local_actions_block');

    $user = $this->createUserWithEmployeeAssignment([
      'maintain organizations',
      'view organizations',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();
    $this->drupalGet(Url::fromRoute('entity.organization.canonical', [
      'organization' => $this->operatingUnit->id(),
    ]));
    $assert_session->statusCodeEquals(200);

    $this->clickLink(t('Edit'));
    $this->assertResponse(200);

    $location = $this->createLocation();
    $edit = [
      // TODO Add more fields.
      'name[0][value]' => $this->randomMachineName(),
      'location[0][target_id]' => $location->label() . ' (' . $location->id() . ')',
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertResponse(200);
    $t_args = [
      '@type' => $this->operatingUnit->type->entity->label(),
      '%title' => $edit['name[0][value]'],
    ];
    $this->assertRaw(t('@type %title has been updated.', $t_args));

    // check cache whether effective.
    $assert_session->responseContains($location->label());
  }

}
