<?php

namespace Drupal\report;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines the access control handler for the report entity type.
 *
 * @see \Drupal\report\Entity\Report
 */
class ReportAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view reports');

      case 'delete':
        if ($entity->isLocked()) {
          return AccessResult::forbidden()->addCacheableDependency($entity);
        }
        else {
          return parent::checkAccess($entity, $operation, $account)->addCacheableDependency($entity);
        }
        break;

      default:
        return parent::checkAccess($entity, $operation, $account);
    }
  }

}
