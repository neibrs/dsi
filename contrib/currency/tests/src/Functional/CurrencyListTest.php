<?php

namespace Drupal\Tests\currency\Functional;

use Drupal\Core\Url;

/**
 * Simple test for currency list.
 *
 * @group currency
 */
class CurrencyListTest extends CurrencyTestBase {

  public function testList() {
    $currency = $this->createCurrency();

    $user = $this->drupalCreateUser([
      'administer currencies',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.currency.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains($currency->label());
  }

}
