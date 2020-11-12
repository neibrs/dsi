<?php


namespace Drupal\Tests\dsi_lawyer\Functional;


use Drupal\Tests\BrowserTestBase;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_lawyer
 */
class LawyerTestBase extends BrowserTestBase {
  /**
   * Modules to enable.
   *
   * @var array
   */

  protected static $modules = ['dsi_lawyer'];

  protected function createLawyer(array $settings = []) {
    $edit = $settings + [
        'name' => $this->randomMachineName(8),
      ];
    return Lawyer::create($edit);
  }

}