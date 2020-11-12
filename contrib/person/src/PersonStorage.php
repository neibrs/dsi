<?php

namespace Drupal\person;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

class PersonStorage extends SqlContentEntityStorage implements PersonStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadOrCreateByName($name, $settings = []) {
    $entities = $this->loadByProperties(['name' => $name]);
    if (!empty($entities)) {
      return reset($entities);
    }

    $settings = ['name' => $name] + $settings;
    $entity = $this->create($settings);
    $entity->save();
    return $entity;
  }

}
