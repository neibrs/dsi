<?php

namespace Drupal\dsi_cases;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Cases entity.
 *
 * @see \Drupal\dsi_cases\Entity\Cases.
 */
class CasesAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\dsi_cases\Entity\CasesInterface $entity */

    switch ($operation) {

      case 'view':

        return AccessResult::allowedIfHasPermission($account, 'view published cases entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit cases entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete cases entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add cases entities');
  }

}
