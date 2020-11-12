<?php

namespace Drupal\grant;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

interface GrantManagerInterface {

  /**
   * @return \Drupal\grant\Entity\GrantInterface[]
   */
  public function getGrants(AccountInterface $account);

  /**
   * @return boolean
   */
  public function hasEntityPermission(EntityInterface $entity, $permission, AccountInterface $account = NULL);

}