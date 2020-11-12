<?php

namespace Drupal\Tests\currency\Functional;

use Drupal\currency\Entity\Currency;
use Drupal\Tests\BrowserTestBase;

abstract class CurrencyTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['currency'];

  /**
   * @param array $settings
   * @return \Drupal\currency\Entity\CurrencyInterface
   */
  protected function createCurrency(array $settings = []) {
    $settings += [
      'id' => strtolower($this->randomMachineName()),
      'label' => $this->randomMachineName(),
    ];
    $currency = Currency::create($settings);
    $currency->save();

    return $currency;
  }

}
