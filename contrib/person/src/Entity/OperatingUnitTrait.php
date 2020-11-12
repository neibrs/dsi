<?php

namespace Drupal\person\Entity;

/**
 * Provides a trait for organization.
 */
trait OperatingUnitTrait {

  public static function getCurrentOperatingUnitId() {
    if ($person = \Drupal::service('person.manager')->currentPerson()) {
      if ($organization = $person->getOrganizationByClassification('operating_unit')) {
        return $organization->id();
      }
    }
  }

}
