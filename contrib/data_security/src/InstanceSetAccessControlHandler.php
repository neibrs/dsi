<?php

namespace Drupal\data_security;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Instance set entity.
 *
 * @see \Drupal\data_security\Entity\InstanceSet.
 */
class InstanceSetAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\data_security\Entity\InstanceSetInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished instance set entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published instance set entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit instance set entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete instance set entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add instance set entities');
  }


}
