<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
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

    // 日期格式处理
    if (preg_match('/^[\d.]*$/', $value)) {
      // 正常的时间处理
      if (strtotime($value)) {
        return date('Y-m-d', strtotime($value));
      } else {
        // 如果是 excel 的时间，则另外处理
        if (empty($this->configuration['to_format'])) {
          return gmdate('Y-m-d', ($value - 25569) * 86400);
        }
        else {
          return gmdate($this->configuration['to_format'], ($value - 25569) * 86400);
        }
      }
    }
    // 字符串格式处理
    else {
      if (empty($this->configuration['from_format']) && empty($this->configuration['to_format'])) {
        if (strtotime($value)) {
          return date('Y-m-d', strtotime($value));
        }
        return '';
      }
      // Validate the configuration.
      if (empty($this->configuration['from_format'])) {
        throw new MigrateException('Format date plugin is missing from_format configuration.');
      }
      if (empty($this->configuration['to_format'])) {
        throw new MigrateException('Format date plugin is missing to_format configuration.');
      }
  
      $fromFormat = $this->configuration['from_format'];
      $toFormat = $this->configuration['to_format'];
      if (isset($this->configuration['timezone'])) {
        @trigger_error('Configuration key "timezone" is deprecated in 8.4.x and will be removed before Drupal 9.0.0, use "from_timezone" and "to_timezone" instead. See https://www.drupal.org/node/2885746', E_USER_DEPRECATED);
        $from_timezone = $this->configuration['timezone'];
        $to_timezone = isset($this->configuration['to_timezone']) ? $this->configuration['to_timezone'] : NULL;
      } else {
        $system_timezone = date_default_timezone_get();
        $default_timezone = !empty($system_timezone) ? $system_timezone : 'UTC';
        $from_timezone = isset($this->configuration['from_timezone']) ? $this->configuration['from_timezone'] : $default_timezone;
        $to_timezone = isset($this->configuration['to_timezone']) ? $this->configuration['to_timezone'] : $default_timezone;
      }
      $settings = isset($this->configuration['settings']) ? $this->configuration['settings'] : [];
  
      // Attempts to transform the supplied date using the defined input format.
      // DateTimePlus::createFromFormat can throw exceptions, so we need to
      // explicitly check for problems.
      try {
        $transformed = DateTimePlus::createFromFormat($fromFormat, $value, $from_timezone, $settings)->format($toFormat, ['timezone' => $to_timezone]);
      } catch (\InvalidArgumentException $e) {
        throw new MigrateException(sprintf("Format date plugin could not transform '%s' using the format '%s' for destination '%s'. Error: %s", $value, $fromFormat, $destination_property, $e->getMessage()), $e->getCode(), $e);
      } catch (\UnexpectedValueException $e) {
        throw new MigrateException(sprintf("Format date plugin could not transform '%s' using the format '%s' for destination '%s'. Error: %s", $value, $fromFormat, $destination_property, $e->getMessage()), $e->getCode(), $e);
      }
  
      return $transformed;
    }

  }

}