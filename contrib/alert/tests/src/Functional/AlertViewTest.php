<?php

namespace Drupal\Tests\alert\Functional;

use Drupal\Core\Url;

/**
 * Simple test for list.
 *
 * @group alert
 */
class AlertViewTest extends AlertTestBase {

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

    $alert = $this->createAlert();

    $user = $this->drupalCreateUser(['view alerts']);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.alert.canonical', [
      'alert' => $alert->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains($alert->label());
  }

}
