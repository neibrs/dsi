<?php

namespace Drupal\Tests\person\Kernel;

use Drupal\Tests\organization\Kernel\OrganizationTestBase;
use Drupal\Tests\person\Traits\PersonTestTrait;
use Drupal\Tests\user\Traits\UserCreationTrait;

abstract class PersonTestBase extends OrganizationTestBase {

  use UserCreationTrait {
    createUser as drupalCreateUser;
    createRole as drupalCreateRole;
    createAdminRole as drupalCreateAdminRole;
  }

  use PersonTestTrait;

  /**
   * {@inheritdoc}
   */
  public static $modules = ['eabax_workflows', 'field', 'file', 'grade', 'image', 'job', 'lookup', 'options', 'person', 'quick_code', 'role_menu', 'telephone'];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installSchema('system', 'sequences');
    $this->installEntitySchema('person');
    $this->installConfig('person');
  }
}
