<?php

namespace Drupal\Tests\person\Functional;

use Drupal\Core\Url;

/**
 * Simple test for person settings.
 *
 * @group person
 */
class PersonSettingsTest extends PersonTestBase {

  /**
   * Tests person settings.
   */
  public function testPersonSettings() {
    $user = $this->drupalCreateUser([
      'administer persons',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('person.settings'));
    $assert_session->statusCodeEquals(200);
  }

}
