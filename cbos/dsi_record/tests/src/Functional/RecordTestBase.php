<?php

namespace Drupal\Tests\dsi_record\Functional;

use Drupal\dsi_record\Entity\Record;
use Drupal\Tests\BrowserTestBase;

/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_record
 */
abstract class RecordTestBase extends BrowserTestBase {
  /**
   * Modules to enable.
   *
   * @var array
   */

  protected static $modules = ['dsi_record'];

  /**
   * @param array $settings
   *
   * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
   */
  protected function createRecord(array $settings = []) {
    $edit = $settings + [
      'name' => $this->randomMachineName(8),
      ];
    return Record::create($edit);
  }

}
