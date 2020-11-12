<?php

namespace Drupal\Tests\role_frontpage\Functional;

use Drupal\Core\Url;
use Drupal\Tests\BrowserTestBase;

/**
 * Simple test for front page.
 *
 * @group role_frontpage
 */
class FrontPageTest extends BrowserTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['role_frontpage'];

  /**
   * Tests the home page.
   */
  public function testLoad() {
    $assert_session = $this->assertSession();

    // Front page for anonymous user must be /user/login
    $this->drupalGet(Url::fromRoute('<front>'));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains('Log in');
  }

}
