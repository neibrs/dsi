<?php

namespace Drupal\Tests\organization\Kernel;

use Drupal\KernelTests\KernelTestBase;
use Drupal\Tests\organization\Traits\OrganizationTestTrait;

class OrganizationTestBase extends KernelTestBase {

  use OrganizationTestTrait;

  public static $modules = ['code', 'currency', 'datetime', 'datetime_range', 'entity_filter', 'location', 'organization', 'pinyin', 'system', 'user', 'views'];

  /**
   * Exempt from strict schema checking.
   *
   * @see \Drupal\Core\Config\Development\ConfigSchemaChecker
   *
   * @var bool
   */
  protected $strictConfigSchema = FALSE;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->installEntitySchema('organization');
    $this->installEntitySchema('user');
    $this->installConfig('organization');

    // Initialize the default_business_group config.
    $default_business_group = $this->createOrganization([
      'classifications' => ['business_group'],
    ]);
    \Drupal::configFactory()->getEditable('organization.settings')
      ->set('default_business_group', $default_business_group->id())
      ->save();
  }

}
