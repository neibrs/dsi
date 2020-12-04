<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\Plugin\migrate\process\FormatDate as FormatDateBase;
use Drupal\migrate\Row;

class FormatDate extends FormatDateBase {
  
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value) && $value !== '0' && $value !== 0) {
      return '';
    }

    // 2020-05-01
    $matches = [];
    if (preg_match('/(\d{4})-(\d{1,2})-(\d{1,2})/', $value, $matches)) {
      return date('Y-m-d', strtotime($matches[1] . '-' . $matches[2] . '-' . $matches[3]));
    }
    // 2020-05
    if (preg_match('/(\d{4})-(\d{1,2})/', $value, $matches)) {
      return date('Y-m-d',strtotime($matches[1] . '-' . $matches[2]));
    }
    // 2020.05.01
    if (preg_match('/(\d{4})\.(\d{1,2})\.(\d{1,2})/', $value, $matches)) {
      return date('Y-m-d',strtotime($matches[1] . '-' . $matches[2] . '-' . $matches[3]));
    }
    // 2020.05
    if (preg_match('/(\d{4})\.(\d{1,2})/', $value, $matches)) {
      return date('Y-m-d',strtotime($matches[1] . '-' . $matches[2]));
    }
    // 2020/05/01
    if (preg_match('/(\d{4})\/(\d{1,2})\/(\d{1,2})/', $value, $matches)) {
      return date('Y-m-d',strtotime($matches[1] . '-' . $matches[2] . '-' . $matches[3]));
    }
    // 2020/05
    if (preg_match('/(\d{4})\/(\d{1,2})/', $value, $matches)) {
      return date('Y-m-d',strtotime($matches[1] . '-' . $matches[2]));
    }
    // 2020
    if (preg_match('/^((19|20)\d{2})(\D|$)/', $value, $matches)) {
      return $matches[1] . '-01-01';
    }

    // excel 时间格式
    if (is_numeric($value)) {
      return gmdate('Y-m-d', ($value - 25569) * 86400);
    }
 

    $this->messenger()->addWarning($this->t('@value : Date format is incorrect.', [
      '@value' => $value,
    ]));
    throw new MigrateSkipProcessException($this->t('@value : Date format is incorrect.', [
      '@value' => $value,
    ]));
  }

}