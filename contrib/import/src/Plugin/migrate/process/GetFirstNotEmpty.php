<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\migrate\process\Get;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "get_first_not_empty"
 * )
 */
class GetFirstNotEmpty extends Get {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $return = parent::transform($value, $migrate_executable, $row, $destination_property);
    if (is_array($return)) {
      foreach ($return as $value) {
        $value = trim($value);
        if (!empty($value)) {
          return $value;
        }
      }
    }
    else {
      return $return;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return FALSE;
  }

}
