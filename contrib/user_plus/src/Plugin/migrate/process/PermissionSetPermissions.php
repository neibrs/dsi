<?php

namespace Drupal\user_plus\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "permission_set_permissions",
 *   handle_multiples = TRUE
 * )
 */
class PermissionSetPermissions extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $permissions = \Drupal::service('user.permissions')->getPermissions();

    $values = explode(',', $value);
    $values = array_map('trim', $values);
    $found = array_filter($permissions, function ($item) use ($values) {
      return in_array((string)$item['title'], $values);
    });
    return ['value' => array_keys($found)];
  }

}

