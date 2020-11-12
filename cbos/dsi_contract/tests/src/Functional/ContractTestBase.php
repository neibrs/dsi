<?php


namespace Drupal\Tests\dsi_contract\Functional;


use Drupal\dsi_contract\Entity\Contract;
use Drupal\Tests\BrowserTestBase;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_contract
 */
abstract class ContractTestBase extends BrowserTestBase {
  /**
   * Modules to enable.
   *
   * @var array
   */

  protected static $modules = ['dsi_contract'];

  /**
   * @param array $settings
   *
   * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
   */
  protected function createContract(array $settings = []) {
    $edit = $settings + [
      'name' => $this->randomMachineName(8),
      ];
    return Contract::create($edit);
  }

}