<?php

namespace Drupal\Tests\organization\Functional;

use Drupal\Core\Url;
use Drupal\organization\Entity\OrganizationType;

/**
 * Simple test for list.
 *
 * @group organization
 */
class OrganizationTypeViewTest extends OrganizationTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block'];

  /**
   * Tests canonical page.
   */
  public function testCanonical() {
    $this->drupalPlaceBlock('page_title');

    $organization_type = OrganizationType::load('department');

    $user = $this->drupalCreateUser([
      'administer organizations',
      'view organizations',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.organization_type.canonical', [
      'organization_type' => $organization_type->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains($organization_type->label());
  }

}
