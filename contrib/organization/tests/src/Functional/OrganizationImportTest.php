<?php

namespace Drupal\Tests\organization\Functional;

use Drupal\Core\StreamWrapper\PublicStream;
use Drupal\Core\Url;

/**
 * Simple test for organization import.
 *
 * @group organization
 */
class OrganizationImportTest extends OrganizationTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block', 'employee_assignment'];

  /**
   * Tests import
   */
  public function testImport() {
    $this->drupalPlaceBlock('local_actions_block');

    $user = $this->drupalCreateUser([
      'view organizations',
      'maintain organizations',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.organization.collection'));
    $assert_session->linkExists(t('Import'));

    $this->clickLink(t('Import'));
    $assert_session->statusCodeEquals(200);

    $source = '/var/www/html/modules/dsi/contrib/organization/tests/data/organization.xls';
    $file = \Drupal::service('file_system')->copy($source, PublicStream::basePath());
    $edit = [
      'migration' => 'organization_xls',
      'file' => $file,
    ];
    $this->drupalPostForm(NULL, $edit, t('Import'));
    $assert_session->statusCodeEquals(200);
    // TODO: Tests imported data
  }

}
