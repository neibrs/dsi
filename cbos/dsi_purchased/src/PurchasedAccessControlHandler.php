<?php

namespace Drupal\dsi_purchased;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Purchased entity.
 *
 * @see \Drupal\dsi_purchased\Entity\Purchased.
 */
class PurchasedAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\dsi_purchased\Entity\PurchasedInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished purchased entities');
        }

        return AccessResult::allowedIfHasPermission($account, 'view published purchased entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit purchased entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete purchased entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add purchased entities');
  }

}
