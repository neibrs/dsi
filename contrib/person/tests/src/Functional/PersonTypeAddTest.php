<?php

namespace Drupal\Tests\person\Functional;

use Drupal\Core\Url;

/**
 * Simple test for person_type add.
 *
 * @group person
 */
class PersonTypeAddTest extends PersonTestBase {

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
      'administer persons',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.person_type.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Add'));

    $this->clickLink(t('Add'));
    $assert_session->statusCodeEquals(200);

    $edit = [
      'id' => strtolower($this->randomMachineName()),
      'label' => $this->randomMachineName(),
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains(t('Created the %label Person type.', [
      '%label' => $edit['label'],
    ]));
  }

}
