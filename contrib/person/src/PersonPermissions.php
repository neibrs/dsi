<?php

namespace Drupal\person;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\person\Entity\PersonType;

/**
 * Provides dynamic permissions for persons of different types.
 */
class PersonPermissions {

  use StringTranslationTrait;

  /**
   * Returns an array of person type permissions.
   *
   * @return array
   *   The person type permissions.
   *   @see \Drupal\user\PermissionHandlerInterface::getPermissions()
   */
  public function personTypePermissions() {
    $perms = [];
    // Generate person permissions for all person types.
    foreach (PersonType::loadMultiple() as $type) {
      $perms += $this->buildPermissions($type);
    }

    return $perms;
  }

  /**
   * Returns a list of person permissions for a given person type.
   *
   * @param \Drupal\person\Entity\PersonType $type
   *   The person type.
   *
   * @return array
   *   An associative array of permission names and descriptions.
   */
  protected function buildPermissions(PersonType $type) {
    $type_id = $type->id();
    $type_params = ['@type_name' => $type->label()];

    return [
      "maintain $type_id persons" => [
        'title' => $this->t('@type_name: Maintain persons', $type_params),
      ],
      "view $type_id persons" => [
        'title' => $this->t('@type_name: View persons', $type_params),
      ],
    ];
  }

}
