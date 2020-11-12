<?php

namespace Drupal\Tests\currency\Functional;

use Drupal\Core\Url;

/**
 * Simple test for currency add form.
 *
 * @group currency
 */
class CurrencyAddTest extends CurrencyTestBase {

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
      'administer currencies',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.currency.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Add Currency'));

    $this->clickLink(t('Add Currency'));
    $assert_session->statusCodeEquals(200);

    $edit = [
      'id' => strtolower($this->randomMachineName()),
      'label' => $this->randomMachineName(),
    ];
    $this->drupalPostForm(NULL, $edit, t('Save'));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains(t('Created the %label Currency.', [
      '%label' => $edit['label'],
    ]));
  }

}
