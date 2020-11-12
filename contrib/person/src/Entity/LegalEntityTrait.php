<?php

namespace Drupal\person\Entity;

/**
 * Provides a trait for legal entity.
 */
trait LegalEntityTrait {

  public static function getCurrentLegalEntityId() {
    if ($person = \Drupal::service('person.manager')->currentPerson()) {
      if ($organization = $person->getOrganizationByClassification('legal_entity')) {
        return $organization->id();
      }
    }
  }

}
