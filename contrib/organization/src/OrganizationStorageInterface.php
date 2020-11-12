<?php

namespace Drupal\organization;

use Drupal\Core\Entity\ContentEntityStorageInterface;

interface OrganizationStorageInterface extends ContentEntityStorageInterface {

  /**
   * @param $parent_id
   * @param $classification
   * @return \Drupal\organization\Entity\OrganizationInterface[]
   */
  public function loadChildrenByClassification($parent_id, $classification);

  /**
   * @param $name
   * @param array $settings
   * @return \Drupal\organization\Entity\OrganizationInterface
   */
  public function loadOrCreateByName($name, $settings = []);

  /**
   * @param $parent_id
   * @param array $children
   * @param bool $status
   * @return \Drupal\organization\Entity\OrganizationInterface[]
   */
  public function loadAllChildren($parent_id, $children = [], $status = FALSE);

  /**
   *
   * @param \Drupal\organization\Entity\OrganizationInterface|int $organization
   *   The organization.
   *
   * @param $classification
   * @return \Drupal\organization\Entity\OrganizationInterface[]
   */
  public function loadParentsByClassification($organization, $classification);

}
