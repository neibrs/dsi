<?php

namespace Drupal\Tests\responsibility\Functional;

use Drupal\responsibility\Entity\Responsibility;
use Drupal\Tests\BrowserTestBase;

abstract class ResponsibilityTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['responsibility'];

  /**
   * @param array $settings
   * @return \Drupal\responsibility\Entity\ResponsibilityInterface
   */
  protected function createResponsibility(array $settings = []) {
    $settings += [
      'id' => strtolower($this->randomMachineName()),
      'label' => $this->randomMachineName(),
    ];
    $responsibility = Responsibility::create($settings);
    $responsibility->save();

    return $responsibility;
  }

}
