<?php

namespace Drupal\dsi_ipa;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the IP Address.
 *
 * @see \Drupal\dsi_ipa\Entity\Ipa.
 */
class IpaAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\dsi_ipa\Entity\IpaInterface $entity */

    switch ($operation) {
      case 'view':
        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished ip address');
        }
        return AccessResult::allowedIfHasPermission($account, 'view published ip address');

      case 'update':
        return AccessResult::allowedIfHasPermission($account, 'edit ip address');

      case 'delete':
        return AccessResult::allowedIfHasPermission($account, 'delete ip address');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add ip address');
  }

}
