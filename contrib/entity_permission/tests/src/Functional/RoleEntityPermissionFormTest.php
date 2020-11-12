<?php

namespace Drupal\Tests\entity_permission\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Simple test form RoleEntityPermissionForm.
 *
 * @group entity_permission
 */
class RoleEntityPermissionFormTest extends EntityPermissionTestBase {
  /**
   * Exempt from strict schema checking.
   *
   * @see \Drupal\Core\Config\Development\ConfigSchemaChecker
   *
   * @var bool
   */
  protected $strictConfigSchema = FALSE;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['person'];

  /**
   * Tests RoleEntityPermissionForm.
   */
  public function testForm() {
    $user = $this->drupalCreateUser([
      'administer entity permissions',
    ]);
    $this->drupalLogin($user);
    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity_permission.settings'));
    $assert_session->statusCodeEquals(200);

    $edit = [
      'entities[person][entity_permission]' => TRUE,
      'entities[person][bundle_permission]' => TRUE,
      'entities[person][field_permission]' => TRUE,
    ];
    $this->drupalPostForm(NULL, $edit, t('Save configuration'));
    $this->assertResponse(200);

    $this->drupalGet(Url::fromRoute('entity.user_role.entity_permission', [
      'user_role' => 'authenticated',
    ]));
    $this->assertResponse(200);

    $this->drupalPostForm(NULL, [], t('Save'));
    $this->assertResponse(200);
  }

}
