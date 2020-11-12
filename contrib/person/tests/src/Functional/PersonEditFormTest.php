<?php

namespace Drupal\Tests\dsi_person\Functional;

use Drupal\Core\Url;

/**
 * Create a person and test person edit functionality.
 *
 * @group dsi_person
 */
class PersonEditFormTest extends PersonTestBase {

  /**
   * A normal logged in user.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $webUser;

  protected function setUp() {
    parent::setUp();

    $this->webUser = $this->drupalCreateUser([
      'view published person entities'
    ]);
  }

  public function testPersonEdit() {
    $this->drupalLogin($this->webUser);

    $edit = [
      'name' => $this->randomMachineName(8),
    ];
    $person = $this->createPerson($edit);
    $this->drupalPostForm(Url::fromRoute('entity.dsi_person.edit_form', ['person' => $person->id()]));
    $this->assertSession()->statusCodeEquals(200);
  }
}