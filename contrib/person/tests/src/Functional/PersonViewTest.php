<?php

namespace Drupal\Tests\person\Functional;

use Drupal\Core\Url;

/**
 * Simple test for list.
 *
 * @group person
 */
class PersonViewTest extends PersonTestBase {

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

    $person = $this->createPerson();

    $user = $this->drupalCreateUser(['view persons']);
    $this->drupalLogin($user);

    $this->drupalGet(Url::fromRoute('entity.person.canonical', ['person' => $person->id()]));
    $this->assertResponse(200);
    $this->assertSession()->linkExists($person->getName());
  }

}
