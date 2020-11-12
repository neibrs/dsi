<?php

namespace Drupal\Tests\location\Functional;

use Drupal\Tests\BrowserTestBase;
use Drupal\Tests\location\Traits\LocationTestTraits;

abstract class LocationTestBase extends BrowserTestBase {

  use LocationTestTraits;
  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['location'];

}
