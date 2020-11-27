<?php

namespace Drupal\Tests\alert\Functional;

use Drupal\Core\Url;

/**
 * Simple test for alert_type list.
 *
 * @group alert
 */
class AlertTypeListTest extends AlertTestBase {

  public function testList() {
    $alert_type = $this->createAlertType();

    $user = $this->drupalCreateUser([
      'administer alerts',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.alert_type.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains($alert_type->label());
  }

}
