<?php

namespace Drupal\organization;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

class OrganizationStorage extends SqlContentEntityStorage implements OrganizationStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadChildrenByClassification($parent_id, $classification) {
    $children = $this->loadByProperties([
      'parent' => $parent_id,
      'classifications' => $classification,
    ]);

    foreach ($children as $child) {
      if ($childs = $this->loadChildrenByClassification($child->id(), $classification)) {
        $children += $childs;
      }
      return $children;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function loadOrCreateByName($name, $settings = []) {
    $query = $this->getQuery();
    $query->condition($query->orConditionGroup()
      ->condition('name', $name)
      ->condition('description', $name)
    );
    $ids = $query->execute();
    $entities = $this->loadMultiple($ids);
    if (!empty($entities)) {
      return reset($entities);
    }

    $settings = ['name' => $name] + $settings;
    $entity = $this->create($settings);
    $entity->save();
    return $entity;
  }

  /**
   * {@inheritdoc}
   */
  public function loadAllChildren($parent_id, $children = [], $status = FALSE) {
    $query = $this->getQuery();
    $query->condition('parent', $parent_id);
    if ($status) {
      $query->condition('status', $status);
    }
    $ids = $query->execute();
    foreach ($ids as $id) {
      // 避免无限循环.
      if (!isset($children[$id])) {
        $entities = $this->loadAllChildren($id, $children, $status);
        $children += array_map(function ($item) {
          return $item->id();
        }, $entities);
      }

      $children[$id] = $id;
    }

    $children = $this->loadMultiple($children);

    return $children;
  }

  /**
   * {@inheritdoc}
   */
  public function loadParentsByClassification($organization, $classification) {
    if (is_numeric($organization)) {
      $organization = $this->load($organization);
    }

    if ($parent = $organization->getParent()) {
      $parents = [];

      if ($parent->hasClassification($classification)) {
        $parents[$parent->id()] = $parent;
      }

      $parents += $this->loadParentsByClassification($parent, $classification);

      return $parents;
    }

    return [];
  }
}
