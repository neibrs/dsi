<?php

namespace Drupal\Tests\location\Functional;

use Drupal\Core\Url;

/**
 * Simple test for location add.
 *
 * @group location
 */
class LocationAddTest extends LocationTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block'];

  /**
   * Tests add form.
   */
  public function testAddForm() {
    $this->drupalPlaceBlock('local_actions_block');

    $user = $this->drupalCreateUser([
      'add locations',
      'view locations',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.location.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Add Location'));

    $this->clickLink(t('Add Location'));
    $assert_session->statusCodeEquals(200);

    $edit = [
      'name[0][value]' => $this->randomMachineName(),
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains(t('Created the %label Location.', [
      '%label' => $edit['name[0][value]'],
    ]));
  }

}
