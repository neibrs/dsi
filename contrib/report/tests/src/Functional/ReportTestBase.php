<?php

namespace Drupal\Tests\report\Functional;

use Drupal\Tests\entity_filter\Functional\EntityFilterTestBase;

abstract class ReportTestBase extends EntityFilterTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['report'];

}