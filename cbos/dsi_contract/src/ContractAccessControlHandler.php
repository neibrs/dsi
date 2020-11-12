<?php

namespace Drupal\dsi_contract;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Contract entity.
 *
 * @see \Drupal\dsi_contract\Entity\Contract.
 */
class ContractAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\dsi_contract\Entity\ContractInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished contract entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published contract entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit contract entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete contract entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add contract entities');
  }


}
