<?php

namespace Drupal\Tests\person\Functional;

use Drupal\Core\Url;
use Drupal\person\Entity\PersonType;

/**
 * Simple test for person_type list.
 *
 * @group person
 */
class PersonTypeListTest extends PersonTestBase {

  public function testList() {
    $person_type = PersonType::load('employee');

    $user = $this->drupalCreateUser([
      'administer persons',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.person_type.collection'));
    $assert_session->statusCodeEquals(200);
    $assert_session->responseContains($person_type->label());
  }

}
