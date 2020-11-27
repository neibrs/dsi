<?php

namespace Drupal\Tests\alert\Functional;

use Drupal\Core\Url;

/**
 * Simple test for alert_type delete.
 *
 * @group alert
 */
class AlertTypeDeleteTest extends AlertTestBase {

  /**
   * Tests alert_type delete.
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testDelete() {
    // Prepare data
    $type_no_data = $this->createAlertType();
    $type_has_data = $this->createAlertType();
    $this->createAlert([
      'type' => $type_has_data->id(),
    ]);

    $user = $this->drupalCreateUser([
      'administer alerts',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    // Tests delete type with no data
    $this->drupalGet(Url::fromRoute('entity.alert_type.edit_form', [
      'alert_type' => $type_no_data->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Delete'));

    $this->clickLink(t('Delete'));
    $assert_session->statusCodeEquals(200);

    $this->drupalPostForm(NULL, [], t('Delete'));
    $assert_session->responseContains(t('The @entity-type %label has been deleted.', [
      '@entity-type' => t('alert type'),
      '%label' => $type_no_data->label(),
    ]));
    // Tests delete type with data
    $this->drupalGet(Url::fromRoute('entity.alert_type.delete_form', [
      'alert_type' => $type_has_data->id(),
    ]));
    $assert_session->responseContains(t('You can not remove this %entity type until you have removed all of the %type %entity.', [
      '%type' => $type_has_data->label(),
      '%entity' => t('Alert'),
    ]));
  }

}
