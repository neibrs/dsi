<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * If the source evaluates to a configured value, skip processing or whole row.
 *
 * @MigrateProcessPlugin(
 *   id = "skip_on_part_value"
 * )
 *
 * Examples:
 *
 * Example usage with minimal configuration:
 * @code
 *   type:
 *     plugin: skip_on_part_value
 *     source: content
 *     method: row
 *     search: COUNTA
 * @endcode
 */
class SkipOnPartValue extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function row($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (!isset($this->configuration['search'])) {
      throw new MigrateException('"search" must be configured.');
    }
    if (strpos($value, $this->configuration['search'])) {
      throw new MigrateSkipRowException();
    }

    return $value;
  }

}
