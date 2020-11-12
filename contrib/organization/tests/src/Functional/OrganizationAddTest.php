<?php

namespace Drupal\Tests\organization\Functional;

use Drupal\Core\Url;
use Drupal\Tests\employee_assignment\Traits\EmployeeAssignmentTestTrait;

/**
 * Simple test for organization add.
 *
 * @group organization
 */
class OrganizationAddTest extends OrganizationTestBase {

  // 通过 employee_assignment 获得用户所在工作组.
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
  public function testAdd() {
    $this->drupalPlaceBlock('local_actions_block');

    $user = $this->createUserWithEmployeeAssignment([
      'maintain organizations',
      'view organizations',
    ]);
    $this->drupalLogin($user);

    $this->drupalGet(Url::fromRoute('entity.organization.collection'));
    $this->assertResponse(200);
    $this->assertLink(t('Add'));

    $this->clickLink(t('Add'));
    $this->assertResponse(200);

    $this->clickLink(t('Company'));
    $edit = [
      'name[0][value]' => $this->randomMachineName(),
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $this->assertResponse(200);
    $t_args = ['@type' => 'Company', '%title' => $edit['name[0][value]']];
    $this->assertRaw(t('@type %title has been created.', $t_args));
  }

}
