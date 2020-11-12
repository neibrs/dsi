<?php


namespace Drupal\Tests\dsi_cases\Functional;


use Drupal\dsi_cases\Entity\Cases;
use Drupal\Tests\BrowserTestBase;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_cases
 */
abstract class CasesTestBase extends BrowserTestBase {
  /**
   * Modules to enable.
   *
   * @var array
   */

  protected static $modules = ['dsi_cases'];

  /**
   * @param array $settings
   *
   * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
   */
  protected function createCases(array $settings = []) {
    $edit = $settings + [
      'name' => $this->randomMachineName(8),
      ];
    return Cases::create($edit);
  }

}