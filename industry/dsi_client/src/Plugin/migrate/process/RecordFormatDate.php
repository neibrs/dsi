<?php

namespace Drupal\dsi_client\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\Plugin\migrate\process\FormatDate as FormatDateBase;
use Drupal\migrate\Row;

/**
  * @MigrateProcessPlugin(
  *   id = "record_format_date"
  * )
  */
class RecordFormatDate extends FormatDateBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value) && $value !== '0' && $value !== 0) {
      return '';
    }
    // excel 时间格式
    if (is_float($value)) {
      return implode('T', explode(' ', gmdate('Y-m-d H:i:s', ($value - 25569) * 86400)));
    }

    return implode('T', explode(' ', $value));

    $this->messenger()->addWarning($this->t('@value : Date format is incorrect.', [
      '@value' => $value,
    ]));
    throw new MigrateSkipProcessException($this->t('@value : Date format is incorrect.', [
      '@value' => $value,
    ]));
  }

}
