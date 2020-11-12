<?php

namespace Drupal\person\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;

class PersonTypeCheck implements AccessInterface {

  public function access(Route $route, AccountInterface $account) {
    $types = $route->getRequirement('_person_type');

    if ($types === NULL) {
      return AccessResult::neutral();
    }

    $user = \Drupal::entityTypeManager()->getStorage('user')->load($account->id());
    if (!$person = $user->person->entity) {
      return AccessResult::neutral();
    }

    $types = explode(',', $types);
    $query = \Drupal::entityQuery('person_type');
    $query->condition($query->orConditionGroup()
      ->condition('id', $types, 'IN')
      ->condition('system_type', $types, 'IN')
    );
    $types = $query->execute();
    return AccessResult::allowedIf(in_array($person->type->target_id, $types));
  }

}