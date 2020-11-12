<?php

namespace Drupal\Tests\location\Functional;

use Drupal\Core\Url;

/**
 * Simple test for list.
 *
 * @group location
 */
class LocationViewTest extends LocationTestBase {

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
    $this->drupalPlaceBlock('page_title_block');

    $location = $this->createLocation();

    $user = $this->drupalCreateUser(['view locations']);
    $this->drupalLogin($user);

    $this->drupalGet(Url::fromRoute('entity.location.canonical', ['location' => $location->id()]));
    $this->assertResponse(200);
    $this->assertText($location->label());
  }

}
