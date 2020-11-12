<?php

namespace Drupal\organization;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Organization type entity.
 *
 * @see \Drupal\organization\Entity\OrganizationType.
 */
class OrganizationTypeAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if ($operation == 'view') {
      return AccessResult::allowedIfHasPermission($account, 'view organizations');
    }
    else {
      return parent::checkAccess($entity, $operation, $account);
    }
  }

}
