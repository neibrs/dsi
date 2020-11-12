<?php


namespace Drupal\Tests\dsi_litigant\Functional;


use Drupal\dsi_litigant\Entity\Litigant;
use Drupal\Tests\BrowserTestBase;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_litigant
 */
abstract class LitigantTestBase extends BrowserTestBase {
  /**
   * Modules to enable.
   *
   * @var array
   */

  protected static $modules = ['dsi_litigant'];

  /**
   * @param array $settings
   *
   * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
   */
  protected function createLitigant(array $settings = []) {
    $edit = $settings + [
      'name' => $this->randomMachineName(8),
      ];
    return Litigant::create($edit);
  }

}