<?php

namespace Drupal\dsi_finance;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Finance entity.
 *
 * @see \Drupal\dsi_finance\Entity\Finance.
 */
class FinanceAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\dsi_finance\Entity\FinanceInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished finance entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published finance entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit finance entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete finance entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add finance entities');
  }


}
