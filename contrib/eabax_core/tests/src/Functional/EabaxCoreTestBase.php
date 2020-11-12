<?php

namespace Drupal\Tests\eabax_core\Functional;

use Drupal\Tests\BrowserTestBase;

abstract class EabaxCoreTestBase extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * The config_translation module is used for testing multiple language environment.
   *
   * @var array
   */
  public static $modules = ['eabax_core', 'config_translation'];

  /**
   * @var \Drupal\user\UserInterface
   */
  protected $implementorUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->implementorUser = $this->drupalCreateUser();
    $this->implementorUser->addRole('implementor');
    $this->implementorUser->save();
  }

}
