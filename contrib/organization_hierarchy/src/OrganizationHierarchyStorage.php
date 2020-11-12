<?php

namespace Drupal\organization_hierarchy;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

class OrganizationHierarchyStorage extends SqlContentEntityStorage implements OrganizationHierarchyStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function loadOrCreateActiveHierarchy($organization_id, $hierarchy_name = NULL) {
    if (!$hierarchy_name) {
      $hierarchy_name = \Drupal::config('organization_hierarchy.settings')->get('default_hierarchy');
    }

    $ids = $this->getQuery()
      ->condition('name', $hierarchy_name)
      ->condition('organization', $organization_id)
      ->condition('status', TRUE)
      ->sort('version', 'DESC')
      ->range(0, 1)
      ->execute();
    if ($id = reset($ids)) {
      return $this->load($id);
    }

    $entity = $this->create([
      'name' => $hierarchy_name,
      'organization' => $organization_id,
      'status' => TRUE,
    ]);
    $entity->save();

    return $entity;
  }

}
