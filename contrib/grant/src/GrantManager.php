<?php

namespace Drupal\grant;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;

class GrantManager implements GrantManagerInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function getGrants(AccountInterface $account) {
    $grant_storage = $this->entityTypeManager->getStorage('grant');
    $query = $grant_storage->getQuery();
    $query->condition($query->orConditionGroup()
      ->condition('grantee_type', 'all_users')
      ->condition($query->andConditionGroup()
        ->condition('grantee_type', 'group_of_users')
        ->condition('grantee', $account->getRoles(), 'IN')
      )
      ->condition($query->andConditionGroup()
        ->condition('grantee_type', 'specific_user')
        ->condition('grantee', $account->id())
      )
      ->condition('status', TRUE)
    );
    $ids = $query->execute();
    return $grant_storage->loadMultiple($ids);
  }

  /**
   * {@inheritdoc}
   */
  public function hasEntityPermission(EntityInterface $entity, $permission, AccountInterface $account = NULL) {
    // User #1 has all privileges.
    if ((int) $account->id() === 1) {
      return TRUE;
    }
    
    $grants = $this->getGrants($account);
    foreach ($grants as $grant) {
      if ($grant->hasEntityPermission($entity, $permission, $account)) {
        return TRUE;
      }
    }

    // 检查角色权限.
    return $account->hasPermission($permission);
  }

}