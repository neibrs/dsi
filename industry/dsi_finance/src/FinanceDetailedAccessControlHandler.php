<?php

namespace Drupal\dsi_finance;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the FinanceDetailed entity.
 *
 * @see \Drupal\dsi_finance\Entity\FinanceDetailed.
 */
class FinanceDetailedAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\dsi_finance\Entity\FinanceDetailedInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished finance_detailed entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published finance_detailed entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit finance_detailed entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete finance_detailed entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add finance_detailed entities');
  }


}
