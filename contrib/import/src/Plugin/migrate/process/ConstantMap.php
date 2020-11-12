<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\Component\Utility\NestedArray;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "constant_map"
 * )
 */
class ConstantMap extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $new_value = $value;
    if (is_array($value)) {
      if (!$value) {
        throw new MigrateException('Can not lookup without a value.');
      }
    }
    else {
      $new_value = [$value];
    }
    $new_value = NestedArray::getValue($this->configuration['map'], $new_value, $key_exists);
    if ($key_exists) {
      $row->setFreezeSource(FALSE);
      $row->setSourceProperty($this->configuration['constant'], $new_value);
      throw new MigrateSkipRowException();
    }
    else {
      return $value;
    }
  }

}
