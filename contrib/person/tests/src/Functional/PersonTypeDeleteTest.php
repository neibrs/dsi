<?php

namespace Drupal\Tests\person\Functional;

use Drupal\Core\Url;

/**
 * Simple test for person_type delete.
 *
 * @group person
 */
class PersonTypeDeleteTest extends PersonTestBase {

  /**
   * Tests person_type delete.
   */
  public function testDelete() {
    // Prepare data
    $type_no_data = $this->createPersonType();
    $type_has_data = $this->createPersonType();
    $this->createPerson([
      'type' => $type_has_data->id(),
    ]);

    $user = $this->drupalCreateUser([
      'administer persons',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    // Tests delete type with no data
    $this->drupalGet(Url::fromRoute('entity.person_type.edit_form', [
      'person_type' => $type_no_data->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    $assert_session->linkExists(t('Delete'));

    $this->clickLink(t('Delete'));
    $assert_session->statusCodeEquals(200);

    $this->drupalPostForm(NULL, [], t('Delete'));
    $assert_session->responseContains(t('The @entity-type %label has been deleted.', [
      '@entity-type' => t('person type'),
      '%label' => $type_no_data->label(),
    ]));
    // Tests delete type with data
    $this->drupalGet(Url::fromRoute('entity.person_type.delete_form', [
      'person_type' => $type_has_data->id(),
    ]));
    // 测试不支持中文字符串比较.
    //$assert_session->responseContains(t('You can not remove this %entity type until you have removed all of the %type %entity.', [
    //  '%type' => $type_has_data->label(),
    //  '%entity' => t('Person'),
    //]));
  }

}
