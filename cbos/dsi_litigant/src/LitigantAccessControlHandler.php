<?php

namespace Drupal\dsi_litigant;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Litigant entity.
 *
 * @see \Drupal\dsi_litigant\Entity\Litigant.
 */
class LitigantAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\dsi_litigant\Entity\LitigantInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished litigant entities');
        }

        return AccessResult::allowedIfHasPermission($account, 'view published litigant entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit litigant entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete litigant entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add litigant entities');
  }

}
