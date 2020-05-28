<?php

namespace Drupal\layout_template;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Layout template entity.
 *
 * @see \Drupal\layout_template\Entity\LayoutTemplate.
 */
class LayoutTemplateAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\layout_template\Entity\LayoutTemplateInterface $entity */

    if ($account->hasPermission('administer layout templates')) {
      return AccessResult::allowed();
    }

    switch ($operation) {
      case 'view':
        if ($entity->isPublic()) {
          return $account->isAuthenticated();
        }
        return AccessResult::allowedIf($account->id() == $entity->getOwnerId());

      case 'update':
      case 'delete':
        if ($account->hasPermission('administer own layout templates')) {
          return AccessResult::allowedIf($account->id() == $entity->getOwnerId());
        }

    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'administer own layout templates');
  }

}
