<?php

namespace Drupal\views_template;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

class ViewsTemplateAccessControlHandler extends EntityAccessControlHandler {
  
  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\views_template\Entity\ViewTemplateInterface $entity */
    switch ($operation) {
      case 'view':
        if ($entity->getIsPublic()) {
          return AccessResult::allowed();
        }
        else {
          return AccessResult::allowedIf($account->id() == $entity->getUserId());
        }
      case 'update':
      case 'delete':
        if ($entity->getUserId() == $account->id()) {
          return AccessResult::allowed();
        }
        elseif ($entity->getIsPublic()) {
          return AccessResult::allowedIfHasPermission($account, 'maintain public view templates');
        }
        else {
          return AccessResult::neutral();
        }
    }
    return parent::checkAccess($entity, $operation, $account);
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowed();
  }

}
