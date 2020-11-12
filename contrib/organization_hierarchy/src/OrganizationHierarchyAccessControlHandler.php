<?php

namespace Drupal\organization_hierarchy;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Organization hierarchy entity.
 *
 * @see \Drupal\organization_hierarchy\Entity\OrganizationHierarchy.
 */
class OrganizationHierarchyAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\organization_hierarchy\Entity\OrganizationHierarchyInterface $entity */
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view organizations');

      case 'update':
      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'maintain organizations');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'maintain organizations');
  }

}
