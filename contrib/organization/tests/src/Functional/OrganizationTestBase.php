<?php

namespace Drupal\Tests\organization\Functional;

use Drupal\organization\Entity\OrganizationType;
use Drupal\Tests\eabax_core\Functional\EabaxCoreTestBase;
use Drupal\Tests\organization\Traits\OrganizationTestTrait;

abstract class OrganizationTestBase extends EabaxCoreTestBase {

  use OrganizationTestTrait {
    setUp as organizationSetup;
  }

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
  public static $modules = ['organization'];

  /**
   * An organization with legal_entity classification.
   *
   * @var \Drupal\organization\Entity\OrganizationInterface
   */
  protected $legalEntity;

  /**
   * An organization with operating_unit classification.
   *
   * @var \Drupal\organization\Entity\OrganizationInterface
   */
  protected $operatingUnit;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->organizationSetup();

    $this->legalEntity = $this->createOrganization([
      'business_group' => $this->businessGroupOrganization->id(),
      'classifications' => ['legal_entity'],
    ]);
    $this->operatingUnit = $this->createOrganization([
      'business_group' => $this->businessGroupOrganization->id(),
      'parent' => $this->legalEntity->id(),
      'classifications' => ['operating_unit'],
    ]);
  }

  /**
   * @param array $settings
   *   (optional) An associative array of settings for the entity, as used in
   *   entity_create().
   *
   * @return \Drupal\organization\Entity\OrganizationType
   */
  protected function createOrganizationType(array $settings = []) {
    $settings += [
      'id' => $this->randomMachineName(),
    ];

    $entity = OrganizationType::create($settings);
    $entity->save();

    return $entity;
  }

}
