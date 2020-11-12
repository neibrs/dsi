<?php

namespace Drupal\Tests\currency\Functional;

use Drupal\Core\Url;

/**
 * Simple test for currency delete.
 *
 * @group currency
 */
class CurrencyDeleteTest extends CurrencyTestBase {

  /**
   * Tests currency delete.
   */
  public function testDelete() {
    $currency = $this->createCurrency();

    $user = $this->drupalCreateUser([
      'administer currencies',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.currency.edit_form', [
      'currency' => $currency->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Delete'));

    $this->clickLink(t('Delete'));
    $assert_session->statusCodeEquals(200);

    $this->drupalPostForm(NULL, [], t('Delete'));
    $assert_session->responseContains(t('The @entity-type %label has been deleted.', [
      '@entity-type' => t('currency'),
      '%label' => $currency->label(),
    ]));
  }

}
