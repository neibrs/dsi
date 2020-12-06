<?php

namespace Drupal\person\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "person_account_lookup",
 * )
 */
class PersonAccountLookup extends ProcessPluginBase {
  
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value)) {
      return 0;
    }
    $user = \Drupal::entityTypeManager()->getStorage('user')->loadByProperties([
      'person' => $value,
    ]);
    $user = reset($user);
    
    return $user->id();
  }
  
}