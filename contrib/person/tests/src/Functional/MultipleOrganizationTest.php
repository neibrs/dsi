<?php

namespace Drupal\Tests\person\Functional;

use Drupal\Core\Url;
use Drupal\Tests\organization\Traits\MultipleOrganizationTestTrait;

/**
 * @group multiple_organization
 */
class MultipleOrganizationTest extends PersonTestBase {

  use MultipleOrganizationTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['employee_assignment', 'organization'];

  /**
   * Test multiple organization.
   */
  public function testMultipleOrganization() {
    $this->initMultipleOrganization();

    $p1 = $this->createPerson();
    $p2 = $this->createPerson();
    $p22 = $this->createPerson();
    $p231 = $this->createPerson();

    $e1 = $this->createEmployeeAssignment([
      'person' => $p1->id(),
      'organization' => $this->o1->id(),
    ]);
    $e2 = $this->createEmployeeAssignment([
      'person' => $p2->id(),
      'organization' => $this->o2->id(),
    ]);
    $e22 = $this->createEmployeeAssignment([
      'person' => $p22->id(),
      'organization' => $this->o22->id(),
    ]);
    $e231 = $this->createEmployeeAssignment([
      'person' => $p231->id(),
      'organization' => $this->o231->id(),
    ]);

    $user = $this->createUser([
      'view persons',
      'maintain persons',
    ]);
    $user->person->target_id = $this->person->id();
    $user->save();

    $this->drupalLogin($user);
    $assert_session = $this->assertSession();

    // Person list page
    $this->drupalGet(Url::fromRoute('entity.person.collection'));
    foreach ([$p2->label(), $p22->label(), $p231->label()] as $label) {
      $assert_session->linkExists($label);
    }
    $assert_session->linkNotExists($p1->label());

    // Search person
    foreach ([$p2->label(), $p22->label(), $p231->label()] as $label) {
      $this->drupalGet(Url::fromRoute('entity.person.collection', [], [
        'query' => ['combine' => $label]
      ]));
      $assert_session->linkExists($label);
    }
    $this->drupalGet(Url::fromRoute('entity.person.collection', [], [
      'query' => ['combine' => $p1->label()]
    ]));
    $assert_session->linkNotExists($p1->label());

    //Export person
    $this->drupalGet(Url::fromRoute('entity.person.export'));
    foreach ([$p2->label(), $p22->label(), $p231->label()] as $label) {
      $assert_session->responseContains($label);
    }
    $assert_session->responseNotContains($p1->label());
  }

}
