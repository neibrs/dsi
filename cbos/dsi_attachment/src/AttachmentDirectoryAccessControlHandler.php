<?php

namespace Drupal\dsi_attachment;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Attachment directory entity.
 *
 * @see \Drupal\dsi_attachment\Entity\AttachmentDirectory.
 */
class AttachmentDirectoryAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\dsi_attachment\Entity\AttachmentDirectoryInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished attachment directory entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published attachment directory entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit attachment directory entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete attachment directory entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add attachment directory entities');
  }


}
