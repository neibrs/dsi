<?php

namespace Drupal\Tests\alert\Functional;

use Drupal\Core\Url;

/**
 * Simple test for alert delete.
 *
 * @group alert
 */
class AlertDeleteTest extends AlertTestBase {

  /**
   * Tests alert delete.
   */
  public function testDelete() {
    $alert = $this->createAlert();

    $user = $this->drupalCreateUser([
      'delete alerts',
      'edit alerts',
      'view alerts',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.alert.edit_form', [
      'alert' => $alert->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Delete'));

    $this->clickLink(t('Delete'));
    $assert_session->statusCodeEquals(200);

    $this->drupalPostForm(NULL, [], t('Delete'));
    $assert_session->responseContains(t('The @entity-type %label has been deleted.', [
      '@entity-type' => t('alert'),
      '%label' => $alert->label(),
    ]));
  }

}
