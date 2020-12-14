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

  /**
   * 只找一级人员.
   */
  public function loadSubordinatesIds($organizations = []) {
    $organizations_ids = array_map(function ($organization) {
      return $organization->id();
    }, $organizations);
    $query = $this->getQuery();
    $query->condition('organization', $organizations_ids, 'IN');
    $ids = $query->execute();

    return $ids;
  }
}
