<?php

namespace Drupal\Tests\organization\Functional;

use Drupal\Core\Url;

/**
 * Simple test for list.
 *
 * @group organization
 */
class OrganizationViewTest extends OrganizationTestBase {

  public static $modules = ['employee_assignment'];

  /**
   * Tests canonical page.
   */
  public function testCanonical() {
    $user = $this->drupalCreateUser(['view organizations']);
    $this->drupalLogin($user);
    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.organization.canonical', [
      'organization' => $this->legalEntity->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains($this->legalEntity->label());
    // Tests children box
    $assert_session->linkExists($this->operatingUnit->label());
  }

}
