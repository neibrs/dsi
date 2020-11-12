<?php

namespace Drupal\Tests\responsibility\Functional;

use Drupal\Core\Url;

/**
 * Simple test for responsibility list.
 *
 * @group responsibility
 */
class ResponsibilityListTest extends ResponsibilityTestBase {

  public function testList() {
    $responsibility = $this->createResponsibility();

    $user = $this->drupalCreateUser([
      'view responsibilities',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.responsibility.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains($responsibility->label());
  }

}
