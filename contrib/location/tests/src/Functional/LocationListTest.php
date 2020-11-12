<?php

namespace Drupal\Tests\location\Functional;

use Drupal\Core\Url;

/**
 * Simple test for location list.
 *
 * @group location
 */
class LocationListTest extends LocationTestBase {

  public function testList() {
    $location = $this->createLocation();

    $user = $this->drupalCreateUser([
      'view locations',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.location.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists($location->label());
  }

}
