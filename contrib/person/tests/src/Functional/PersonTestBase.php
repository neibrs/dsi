<?php

namespace Drupal\Tests\dsi_person\Functional;

use Drupal\dsi_person\Entity\Person;
use Drupal\Tests\BrowserTestBase;

/**
 * Sets up page and article content types.
 */
abstract class PersonTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = ['dsi_person'];

  /**
   * @param array $settings
   */
  protected function createPerson(array $settings = []){
    $edit = $settings + [
      'name' => $this->randomMachineName(8),
    ];

    return Person::create($edit);
  }
}