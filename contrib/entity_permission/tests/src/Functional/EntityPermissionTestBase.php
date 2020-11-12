<?php

namespace Drupal\Tests\entity_permission\Functional;

use Drupal\Tests\BrowserTestBase;

abstract class EntityPermissionTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['entity_permission'];

}
