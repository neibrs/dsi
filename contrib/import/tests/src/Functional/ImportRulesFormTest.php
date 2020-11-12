<?php

namespace Drupal\Tests\import\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Simple test for import rules form.
 *
 * @group import
 */
class ImportRulesFormTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block', 'import'];

  /**
   * Tests import rules form.
   */
  public function testImportRulesForm() {
    $this->drupalPlaceBlock('role_menu_block');

    $user = $this->drupalCreateUser();
    $user->addRole('implementor');
    $user->save();
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('<front>'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Define import rules'));

    $this->clickLink(t('Define import rules'));
    $assert_session->statusCodeEquals(200);
  }

}
