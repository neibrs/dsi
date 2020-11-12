<?php

namespace Drupal\Tests\currency\Functional;

use Drupal\Core\Url;

/**
 * Simple test for list.
 *
 * @group currency
 */
class CurrencyViewTest extends CurrencyTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block'];

  /**
   * Tests canonical page.
   */
  public function testCanonical() {
    $this->drupalPlaceBlock('page_title_block');

    $currency = $this->createCurrency();

    $user = $this->drupalCreateUser([
      'administer currencies',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.currency.canonical', [
      'currency' => $currency->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains($currency->label());
  }

}
