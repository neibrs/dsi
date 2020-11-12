<?php

namespace Drupal\Tests\responsibility\Functional;

use Drupal\Core\Url;

/**
 * Simple test for list.
 *
 * @group responsibility
 */
class ResponsibilityViewTest extends ResponsibilityTestBase {

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

    $responsibility = $this->createResponsibility();

    $user = $this->drupalCreateUser(['view responsibilities']);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.responsibility.canonical', [
      'responsibility' => $responsibility->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains($responsibility->label());
  }

}
