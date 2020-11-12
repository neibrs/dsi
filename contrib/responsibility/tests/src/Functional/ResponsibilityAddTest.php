<?php

namespace Drupal\Tests\responsibility\Functional;

use Drupal\Core\Url;

/**
 * Simple test for responsibility add form.
 *
 * @group responsibility
 */
class ResponsibilityAddTest extends ResponsibilityTestBase {

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
      'maintain responsibilities',
      'view responsibilities',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.responsibility.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Add'));

    $this->clickLink(t('Add'));
    $assert_session->statusCodeEquals(200);

    $edit = [
      'business_group' => 1,
      'name[0][value]' => $this->randomMachineName(),
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains(t('Created the %label Responsibility.', [
      '%label' => $edit['name[0][value]'],
    ]));
  }

}
