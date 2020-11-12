<?php

namespace Drupal\administrative_area;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Administrative area entity.
 *
 * @see \Drupal\administrative_area\Entity\AdministrativeArea.
 */
class AdministrativeAreaAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\administrative_area\Entity\AdministrativeAreaInterface $entity */

    switch ($operation) {
      case 'view':
        return AccessResult::allowedIfHasPermission($account, 'view administrative areas');

      case 'update':
      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'maintain administrative areas');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'maintain administrative areas');
  }

}
