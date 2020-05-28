<?php

namespace Drupal\Tests\eabax_core\Functional;

/**
 * Simple test for role menu pages.
 *
 * @group eabax_core
 */
class RoleMenuPagesTest extends EabaxCoreTestBase {

  use MenuPagesTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block', 'eabax_core'];

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
  }

  /**
   * Tests that the home page loads with a 200 response.
   */
  public function testImplementorMenu() {
    $this->DrupalplaceBlock('role_menu_block');
    $this->drupalLogin($this->implementorUser);
    $this->viewAllPagesOfMenu('role-menu-implementor');
  }

  public function testUserProfile() {
    $assert_session = $this->assertSession();
    $assert_session->statusCodeEquals(200);
  }

}
