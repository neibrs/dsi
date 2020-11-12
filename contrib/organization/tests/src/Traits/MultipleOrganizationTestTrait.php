<?php

namespace Drupal\Tests\organization\Traits;

use Drupal\Tests\employee_assignment\Traits\EmployeeAssignmentTestTrait;
use Drupal\Tests\person\Traits\PersonTestTrait;

trait MultipleOrganizationTestTrait {

  use OrganizationTestTrait;
  use PersonTestTrait;
  use EmployeeAssignmentTestTrait;

  /** @var \Drupal\organization\Entity\OrganizationInterface $o */
  protected $o, $o1, $o2, $o21, $o22, $o23, $o231;

  /** @var \Drupal\person\Entity\PersonInterface */
  protected $person;

  protected function initMultipleOrganization() {
    $this->o = $this->createOrganization([
      'classifications' => ['business_group'],
    ]);
    $this->o1 = $this->createOrganization([
      'parent' => $this->o->id(),
    ]);
    $this->o2 = $this->createOrganization([
      'parent' => $this->o->id(),
      'classifications' => ['business_group'],
    ]);
    $this->o21 = $this->createOrganization([
      'parent' => $this->o2->id(),
    ]);
    $this->o22 = $this->createOrganization([
      'parent' => $this->o2->id(),
    ]);
    $this->o23 = $this->createOrganization([
      'parent' => $this->o2->id(),
      'classifications' => ['business_group'],
    ]);
    $this->o231 = $this->createOrganization([
      'parent' => $this->o23->id(),
    ]);

    $this->person = $this->createPerson();
    $employee_assignment = $this->createEmployeeAssignment([
      'person' => $this->person->id(),
      'organization' => $this->o21->id(),
    ]);
  }
}
