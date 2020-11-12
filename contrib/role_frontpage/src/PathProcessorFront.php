<?php

namespace Drupal\role_frontpage;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\HttpFoundation\Request;

class PathProcessorFront implements InboundPathProcessorInterface {

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountInterface
   */
  protected $currentUser;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(AccountInterface $current_user, EntityTypeManagerInterface $entity_type_manager) {
    $this->currentUser = $current_user;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request) {
    return $this->processRoleFront($path);
  }

  protected function processRoleFront($path) {
    if ($path === '/') {
      $roles = $this->currentUser->getRoles();
      $role_storage = $this->entityTypeManager->getStorage('user_role');
      foreach ($roles as $role) {
        /** @var \Drupal\user\RoleInterface $entity */
        $entity = $role_storage->load($role);
        // Fix: $entity is null.
        if (!$entity) {
          continue;
        }
        $frontpage = $entity->getThirdPartySetting('role_frontpage', 'frontpage', '');
        if (!empty($frontpage)) {
          $path = $frontpage;
          break;
        }
      }
    }
    return $path;
  }

}
