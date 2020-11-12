<?php

namespace Drupal\Tests\location\Functional;

use Drupal\Core\Url;

/**
 * Simple test for location settings.
 *
 * @group location
 */
class LocationSettingsTest extends LocationTestBase {

  /**
   * Tests location settings.
   */
  public function testLocationSettings() {
    $user = $this->drupalCreateUser([
      'administer locations',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('location.settings'));
    $assert_session->statusCodeEquals(200);
  }

}
