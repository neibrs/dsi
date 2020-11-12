<?php

namespace Drupal\grant\Entity;

use Drupal\Core\Entity\EntityAccessControlHandler as EntityAccessControlHandlerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

class EntityAccessControlHandler extends EntityAccessControlHandlerBase {

  /**
   * @var \Drupal\grant\GrantManagerInterface
   */
  protected $grantManager;

  /**
   * Gets the grant manager.
   *
   * @return \Drupal\grant\GrantManagerInterface
   *   The grant manager.
   */
  public function grantManager() {
    if (!$this->grantManager) {
      $this->grantManager = \Drupal::service('grant.manager');
    }
    return $this->grantManager;
  }

}
