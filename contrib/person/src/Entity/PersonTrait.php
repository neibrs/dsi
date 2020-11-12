<?php

namespace Drupal\person\Entity;

/**
 * Provides a trait for person.
 */
trait PersonTrait {

  public static function getCurrentPersonId() {
    if ($person = \Drupal::service('person.manager')->currentPerson()) {
      return $person->id();
    }
  }

}
