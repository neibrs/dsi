<?php

use Drupal\views\ViewExecutable;

/**
 * Implements hook_views_query_substitutions().
 */
function person_views_query_substitutions(ViewExecutable $view) {
  $person_id = 0;
  if ($person = \Drupal::service('person.manager')->currentPerson()) {
    $person_id = $person->id();
  }

  return ['***CURRENT_PERSON***' => $person_id];
}
