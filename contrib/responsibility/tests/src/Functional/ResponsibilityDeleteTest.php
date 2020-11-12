<?php

namespace Drupal\Tests\responsibility\Functional;

use Drupal\Core\Url;

/**
 * Simple test for responsibility delete.
 *
 * @group responsibility
 */
class ResponsibilityDeleteTest extends ResponsibilityTestBase {

  /**
   * Tests responsibility delete.
   */
  public function testDelete() {
    $responsibility = $this->createResponsibility();

    $user = $this->drupalCreateUser([
      'maintain responsibilities',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $delete_label = $responsibility->label();

    $this->drupalGet(Url::fromRoute('entity.responsibility.edit_form', [
      'responsibility' => $responsibility->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Delete'));

    $this->clickLink(t('Delete'));
    $assert_session->statusCodeEquals(200);

    $this->drupalPostForm(NULL, [], t('Delete'));

    $assert_session->responseContains(t('The @entity-type %label has been deleted.', [
      '@entity-type' => 'responsibility',
      '%label' => $delete_label,
    ]));
  }

}
