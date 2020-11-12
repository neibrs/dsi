<?php

namespace Drupal\Tests\organization\Functional;

use Drupal\Core\Url;
use Drupal\organization\Entity\OrganizationType;

/**
 * Simple test for organization_type list.
 *
 * @group organization
 */
class OrganizationTypeListTest extends OrganizationTestBase {

  public function testList() {
    $organization_type = OrganizationType::load('department');

    $user = $this->drupalCreateUser([
      'administer organizations',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.organization_type.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains($organization_type->label());
  }

}
