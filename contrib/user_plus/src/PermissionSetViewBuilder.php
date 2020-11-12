<?php

namespace Drupal\user_plus;

use Drupal\Core\Entity\EntityViewBuilder;

/**
 * View builder handler for permission_set.
 */
class PermissionSetViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function buildComponents(array &$build, array $entities, array $displays, $view_mode) {
    /** @var \Drupal\user_plus\Entity\PermissionSetInterface[] $entities */
    if (empty($entities)) {
      return;
    }

    parent::buildComponents($build, $entities, $displays, $view_mode);

    foreach ($entities as $id => $entity) {
      $bundle = $entity->bundle();
      $display = $displays[$bundle];

      if ($display->getComponent('permissions')) {
        $permission_handler = \Drupal::service('user.permissions');
        $permissions = $permission_handler->getPermissions();
        $permission_set_permissions = $entity->getPermissions();
        $output = [];
        foreach ($permissions as $key => $permission) {
          if (in_array($key, $permission_set_permissions)) {
            $output[] = $permission['title'];
          }
        }

        $build[$id]['permissions'] = [
          '#markup' => implode(', ', $output),
        ];
      }
    }
  }

}