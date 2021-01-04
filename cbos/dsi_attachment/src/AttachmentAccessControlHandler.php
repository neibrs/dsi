<?php

namespace Drupal\dsi_attachment;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Attachment entity.
 *
 * @see \Drupal\dsi_attachment\Entity\Attachment.
 */
class AttachmentAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\dsi_attachment\Entity\AttachmentInterface $entity */

    switch ($operation) {

      case 'view':

        if (!$entity->isPublished()) {
          return AccessResult::allowedIfHasPermission($account, 'view unpublished attachment entities');
        }


        return AccessResult::allowedIfHasPermission($account, 'view published attachment entities');

      case 'update':

        return AccessResult::allowedIfHasPermission($account, 'edit attachment entities');

      case 'delete':

        return AccessResult::allowedIfHasPermission($account, 'delete attachment entities');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'add attachment entities');
  }


}
