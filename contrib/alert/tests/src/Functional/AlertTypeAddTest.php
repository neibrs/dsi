<?php

namespace Drupal\Tests\alert\Functional;

use Drupal\Core\Url;

/**
 * Simple test for alert_type add.
 *
 * @group alert
 */
class AlertTypeAddTest extends AlertTestBase {

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
      'administer alerts',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.alert_type.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Add Alert type'));

    $this->clickLink(t('Add Alert type'));
    $assert_session->statusCodeEquals(200);

    $edit = [
      'id' => strtolower($this->randomMachineName()),
      'label' => $this->randomMachineName(),
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains(t('Created the %label Alert type.', [
      '%label' => $edit['label'],
    ]));
  }

}
