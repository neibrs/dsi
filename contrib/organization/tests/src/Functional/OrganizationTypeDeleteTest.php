<?php

namespace Drupal\Tests\organization\Functional;

use Drupal\Core\Url;

/**
 * Simple test for organization_type delete.
 *
 * @group organization
 */
class OrganizationTypeDeleteTest extends OrganizationTestBase {

  /**
   * Tests organization_type delete.
   */
  public function testDelete() {
    // Prepare data
    $type_no_type = $this->createOrganizationType();
    $type_has_data = $this->createOrganizationType();
    $this->createOrganization([
      'type' => $type_has_data->id(),
    ]);

    $user = $this->drupalCreateUser([
      'administer organizations',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    // Tests delete type with no data
    $this->drupalGet(Url::fromRoute('entity.organization_type.edit_form', [
      'organization_type' => $type_no_type->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Delete'));

    $this->clickLink(t('Delete'));
    $assert_session->statusCodeEquals(200);

    $this->drupalPostForm(NULL, [], t('Delete'));
    $assert_session->responseContains(t('The @entity-type %label has been deleted.', [
      '@entity-type' => t('organization type'),
      '%label' => $type_no_type->label(),
    ]));

    // Tests delete type with data
    $this->drupalGet(Url::fromRoute('entity.organization_type.delete_form', [
      'organization_type' => $type_has_data->id(),
    ]));
    $assert_session->responseContains(t('%type is used by 1 piece of %entity on your site. You can not remove this %entity type until you have removed all of the %type %entity.', [
      '%type' => $type_has_data->label(),
      '%entity' => t('Organization'),
    ]));
  }

}
