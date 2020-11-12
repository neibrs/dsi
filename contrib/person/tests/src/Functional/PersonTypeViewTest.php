<?php

namespace Drupal\Tests\person\Functional;

use Drupal\Core\Url;
use Drupal\person\Entity\PersonType;

/**
 * Simple test for list.
 *
 * @group person
 */
class PersonTypeViewTest extends PersonTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['block'];

  /**
   * Tests canonical page.
   */
  public function testCanonical() {
    $this->drupalPlaceBlock('page_title_block');

    // Prepare data for test
    $person_type = PersonType::load('employee');
    $employee = $this->createPerson(['type' => 'employee']);
    $contact = $this->createPerson(['type' => 'contact']);

    $user = $this->drupalCreateUser([
      'administer persons',
      'view persons',
    ]);
    $this->drupalLogin($user);

    $assert_session = $this->assertSession();

    $this->drupalGet(Url::fromRoute('entity.person_type.canonical', [
      'person_type' => $person_type->id(),
    ]));
    $assert_session->statusCodeEquals(200);
    // Tests the page title
    $assert_session->responseContains($person_type->label());
    // Tests the person list
    $assert_session->linkExists($employee->getName());
    $assert_session->linkNotExists($contact->label());
  }

}
