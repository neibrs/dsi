<?php

namespace Drupal\grant\Entity;

use Drupal\user\Entity\Role as RoleBase;

class Role extends RoleBase {

  /**
   * {@inheritdoc}
   */
  public function hasPermission($permission) {
    if (parent::hasPermission($permission)) {
      return TRUE;
    }

    // 获得当前有效的授权.
    $query = $this->entityTypeManager()->getStorage('grant')->getQuery();
    $query->condition($query->orConditionGroup()
      ->condition('grantee_type', 'all_users')
      ->condition($query->andConditionGroup()
        ->condition('grantee_type', 'group_of_users')
        ->condition('grantee', $this->id())
      )
    );
    \Drupal::service('entity_plus.manager')->addEffectiveDatesCondition($query, date('Y-m-d'));
    $grant_ids = $query->execute();
    if (empty($grant_ids)) {
      return FALSE;
    }

    // 获得授权的权限集.
    $query = \Drupal::database()->select('grant_table');
    $query->condition('id', $grant_ids, 'IN');
    $query->condition('set', NULL, 'IS NOT NULL');
    $query->addField('grant_table', 'set');
    $permission_set_ids = $query->execute()->fetchCol();
    $permission_sets = $this->entityTypeManager()->getStorage('permission_set')
      ->loadMultiple($permission_set_ids);

    // 判断权限集的权限.
    foreach ($permission_sets as $permission_set) {
      if ($permission_set->hasPermission($permission)) {
        return TRUE;
      }
    }

    return FALSE;
  }

}
