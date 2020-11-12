<?php

namespace Drupal\dsi_hardware;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Hardware entity.
 *
 * @see \Drupal\dsi_hardware\Entity\Hardware.
 */
class HardwareAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\dsi_hardware\Entity\HardwareInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished hardware entities');
        }

        return AccessResult::allowedIfHasPermission($account, 'view published hardware entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit hardware entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete hardware entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add hardware entities');
  }

}
