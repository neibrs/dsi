<?php

namespace Drupal\Tests\organization\Traits;

use Drupal\organization\Entity\Organization;

trait OrganizationTestTrait {

  /**
   * @var \Drupal\organization\Entity\OrganizationInterface
   */
  protected $businessGroupOrganization;

  protected function setUp() {
    $this->businessGroupOrganization = $this->createOrganization();
  }

  /**
   * @param array $settings
   *   (optional) An associative array of settings for the organization, as used in
   *   entity_create().
   *
   * @return \Drupal\organization\Entity\OrganizationInterface
   *   The created organization entity.
   */
  protected function createOrganization(array $settings = []) {
    $settings += [
      'type' => 'department',
      'name' => $this->randomMachineName(),
    ];
    $entity = Organization::create($settings);
    $entity->save();

    return $entity;
  }

}
