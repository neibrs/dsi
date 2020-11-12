<?php


namespace Drupal\Tests\dsi_contact\Functional;


use Drupal\dsi_contact\Entity\Contact;
use Drupal\Tests\BrowserTestBase;
/**
 * Simple test to ensure that main page loads with module enabled.
 *
 * @group dsi_contact
 */
abstract class ContactTestBase extends BrowserTestBase {
  /**
   * Modules to enable.
   *
   * @var array
   */

  protected static $modules = ['dsi_contact'];

  /**
   * @param array $settings
   *
   * @return \Drupal\Core\Entity\EntityBase|\Drupal\Core\Entity\EntityInterface
   */
  protected function createContact(array $settings = []) {
    $edit = $settings + [
      'name' => $this->randomMachineName(8),
      ];
    return Contact::create($edit);
  }

}