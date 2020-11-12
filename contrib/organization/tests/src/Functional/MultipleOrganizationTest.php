<?php

namespace Drupal\Tests\organization\Functional;

use Drupal\Core\Url;
use Drupal\Tests\organization\Traits\MultipleOrganizationTestTrait;

/**
 * @group multiple_organization
 */
class MultipleOrganizationTest extends OrganizationTestBase {

  use MultipleOrganizationTestTrait;

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = ['employee_assignment', 'person', 'organization_manager'];

  /**
   * Test multiple organization.
   */
  public function testMultipleOrganization() {
    $this->initMultipleOrganization();

    $user = $this->createUser([
      'view organizations',
      'maintain organizations',
    ]);
    $user->person->target_id = $this->person->id();
    $user->save();

    $this->drupalLogin($user);
    $assert_session = $this->assertSession();

    // Organization list page
    $this->drupalGet(Url::fromRoute('entity.organization.collection'));
    foreach ([$this->o2->label(), $this->o22->label(), $this->o231->label()] as $label) {
      $assert_session->linkExists($label);
    }
    $assert_session->linkNotExists($this->o1->label());

    foreach ([$this->o2, $this->o22, $this->o231] as $organization) {
      // Search organization
      $this->drupalGet(Url::fromRoute('entity.organization.collection', [], [
        'query' => ['combine' => $organization->label()]
      ]));
      $assert_session->linkExists($organization->label());
    }
    // Search organization
    $this->drupalGet(Url::fromRoute('entity.organization.collection', [], [
      'query' => ['combine' => $this->o1->label()]
    ]));
    $assert_session->linkNotExists($this->o1->label());

    //Export organization
    $this->drupalGet(Url::fromRoute('entity.organization.export'));
    foreach ([$this->o2->label(), $this->o22->label(), $this->o231->label()] as $label) {
      $assert_session->responseContains($label);
    }
    $assert_session->responseNotContains($this->o1->label());

  }

  /**
   * Tests for organization test.
   */
  public function testOrganizationSelect() {
    $this->initMultipleOrganization();

    $user = $this->createUser([
      'view organizations',
    ]);
    $user->person->target_id = $this->person->id();
    $user->save();

    $this->drupalLogin($user);
    $assert_session = $this->assertSession();

    $field_definition = $this->o->getFieldDefinition('parent');
    $handler = $this->container->get('plugin.manager.entity_reference_selection')->getSelectionHandler($field_definition);
    $result = $handler->getReferenceableEntities();
    $this->assertArrayNotHasKey($this->o->id(), $result['department']);
    $this->assertArrayNotHasKey($this->o1->id(), $result['department']);
    $this->assertArrayHasKey($this->o2->id(), $result['department']);
    $this->assertArrayHasKey($this->o21->id(), $result['department']);
    $this->assertArrayHasKey($this->o22->id(), $result['department']);
    $this->assertArrayHasKey($this->o23->id(), $result['department']);
    $this->assertArrayHasKey($this->o231->id(), $result['department']);
  }

}
