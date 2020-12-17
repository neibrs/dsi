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
   * 找N级人员.
   */
  public function loadSubordinatesIds($organizations = []) {
    $ids = array_map(function ($organization) {
      return $organization->id();
    }, $organizations);
    // 负责部门的所有子部门
    $sub_ids = \Drupal::entityTypeManager()
      ->getStorage('organization')
      ->loadAllChildren($ids);
    $sub_ids = array_map(function ($organization) {
      return $organization->id();
    }, $sub_ids);
    $query = $this->getQuery();
    $query->condition('organization', array_merge($sub_ids, $ids), 'IN');
    $ids = $query->execute();

    return $ids;
  }
}
