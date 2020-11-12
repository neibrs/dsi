<?php

namespace Drupal\Tests\views_plus\Functional;

use Drupal\Tests\BrowserTestBase;

class ViewsPlusTestBase extends BrowserTestBase {

  /**
   * Exempt from strict schema checking.
   *
   * @see \Drupal\Core\Config\Development\ConfigSchemaChecker
   *
   * @var bool
   */
  protected $strictConfigSchema = FALSE;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['views_plus'];

}
