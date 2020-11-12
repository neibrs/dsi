<?php

namespace Drupal\Tests\location\Functional;

use Drupal\Core\Url;

/**
 * Simple test for location delete.
 *
 * @group location
 */
class LocationDeleteTest extends LocationTestBase {

  /**
   * Tests location delete.
   */
  public function testDelete() {
    $location = $this->createLocation();

    $user = $this->drupalCreateUser([
      'delete locations',
      'edit locations',
      'view locations',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.location.edit_form', [
      'location' => $location->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Delete'));

    $this->clickLink(t('Delete'));
    $assert_session->statusCodeEquals(200);

    $this->drupalPostForm(NULL, [], t('Delete'));
    $assert_session->responseContains(t('The @entity-type %label has been deleted.', [
      '@entity-type' => t('location'),
      '%label' => $location->label(),
    ]));
  }

}
