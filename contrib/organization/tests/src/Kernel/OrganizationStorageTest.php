<?php

namespace Drupal\Tests\organization\Kernel;

use Drupal\organization\Entity\Organization;
use Drupal\Tests\token\Kernel\KernelTestBase;

/**
 * Tests for organization storage.
 *
 * @group organization
 */
class OrganizationStorageTest extends OrganizationTestBase {

  /**
   * Tests loadOrCreateByName.
   */
  public function testLoadOrCreateByName() {
    $org1 = Organization::create(['type' => 'department', 'name' => 'org1']);
    $org1->save();
    $org2 = Organization::create(['type' => 'department', 'description' => 'org2']);
    $org2->save();

    $organization_storage = \Drupal::entityTypeManager()->getStorage('organization');
    $this->assertTrue($organization_storage->loadOrCreateByName('org1')->id() == $org1->id(), 'Found by name.');
    $this->assertTrue($organization_storage->loadOrCreateByName('org2')->id() == $org2->id(), 'Found by description.');
  }

}
