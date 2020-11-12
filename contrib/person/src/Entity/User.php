<?php

namespace Drupal\person\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\user\Entity\User as UserBase;

class User extends UserBase {

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    // Make sure that the employee role are not persisted.
    foreach ($this->get('roles') as $index => $item) {
      if ($item->target_id == 'employee') {
        $this->get('roles')->offsetUnset($index);
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRoles($exclude_locked_roles = FALSE) {
    $roles = parent::getRoles($exclude_locked_roles);

    // Users with an employee person type always have the employee user role.
    if (!$exclude_locked_roles) {
      /** @var \Drupal\person\Entity\PersonInterface $person */
      if ($person = $this->get('person')->entity) {
        if ($person->getType()->isEmployee()) {
          $roles[] = 'employee';
        }
      }
    }

    return $roles;
  }

}
