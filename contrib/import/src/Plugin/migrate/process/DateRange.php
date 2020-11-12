<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\migrate\process\FormatDate;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "date_range"
 * )
 */
class DateRange extends FormatDate {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // parent::transform 要求要设置 from_format 和 to_format
    if (!isset($this->configuration['from_format'])) {
      $this->configuration['from_format'] = 'Y-m-d';
    }
    if (!isset($this->configuration['to_format'])) {
      $this->configuration['to_format'] = 'Y-m-d';
    }

    if (is_numeric($value)) {
      // Excel date format.
      $time = ((int) $value - 25569) * 24 * 60 * 60;
      return ['value' => NULL, 'end_value' => date('Y-m-d', $time)];
    }
    else {
      $dates = explode(' - ', $value);
      if (count($dates) > 1) {
        return [
          'value' => parent::transform(trim($dates[0]), $migrate_executable, $row, $destination_property),
          'end_value' => parent::transform(trim($dates[1]), $migrate_executable, $row, $destination_property),
        ];
      }
      else {
        return [
          'value' => NULL,
          'end_value' => parent::transform(trim($dates[0]), $migrate_executable, $row, $destination_property),
        ];
      }
    }
  }

}
