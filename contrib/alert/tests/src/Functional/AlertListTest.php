<?php

namespace Drupal\Tests\alert\Functional;

use Drupal\Core\Url;

/**
 * Simple test for alert list.
 *
 * @group alert
 */
class AlertListTest extends AlertTestBase {

  public function testList() {
    $alert = $this->createAlert();

    $user = $this->drupalCreateUser([
      'view alerts',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.alert.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists($alert->label());
  }

}
