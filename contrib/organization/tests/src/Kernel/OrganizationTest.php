<?php

namespace Drupal\Tests\organization\Kernel;

use Drupal\Tests\organization\Traits\OrganizationTestTrait;

/**
 * Tests for organization.
 *
 * @group organization
 */
class OrganizationTest extends OrganizationTestBase {
  use OrganizationTestTrait;

  /**
   * Tests getByClassification.
   */
  public function testGetByClassification() {
    $top = $this->createOrganization([
      'classifications' => ['business_group'],
    ]);
    $middle = $this->createOrganization([
      'classifications' => ['business_group'],
      'parent' => $top->id(),
    ]);
    $child = $this->createOrganization([
      'parent' => $middle->id(),
    ]);


    $this->assertEqual($middle->business_group->target_id, $top->id());
    $this->assertEqual($child->getByClassification('business_group')->id(), $middle->id());
  }

}
