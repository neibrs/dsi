<?php

namespace Drupal\role_frontpage;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Session\AccountInterface;

class RoleFrontpageManager implements RoleFrontpageManagerInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  public function getFrontpage(AccountInterface $account = NULL) {
    if (!$account) {
      $account = \Drupal::currentUser();
    }
    $roles = $account->getRoles();
    $role_storage = $this->entityTypeManager->getStorage('user_role');
    foreach ($roles as $role) {
      /** @var \Drupal\user\RoleInterface $entity */
      if ($entity = $role_storage->load($role)) {
        $frontpage = $entity->getThirdPartySetting('role_frontpage', 'frontpage', '');
        if (!empty($frontpage)) {
          return $frontpage;
        }
      }
    }
  }

}
