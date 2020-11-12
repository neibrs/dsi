<?php

namespace Drupal\organization_hierarchy;

use Drupal\Core\Entity\ContentEntityStorageInterface;

interface OrganizationHierarchyStorageInterface extends ContentEntityStorageInterface {

  /**
   * @return \Drupal\organization_hierarchy\Entity\OrganizationHierarchyInterface
   */
  public function loadOrCreateActiveHierarchy($organization_id, $hierarchy_name = NULL);

}
