<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipProcessException;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Plugin\migrate\process\SkipOnEmpty as SkipOnEmptyBase;
use Drupal\migrate\Row;

class SkipOnEmpty extends SkipOnEmptyBase {

  /**
   * {@inheritdoc}
   */
  public function row($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $invert = isset($this->configuration['invert']);
    if (!$value && !$invert || $value && $invert) {
      $message = !empty($this->configuration['message']) ? $this->configuration['message'] : NULL;
      throw new MigrateSkipRowException($message);
    }
    return $value;
  }

  /**
   * {@inheritdoc}
   */
  public function process($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $invert = isset($this->configuration['invert']);
    if (!$value && !$invert || $value && $invert) {
      throw new MigrateSkipProcessException();
    }
    return $value;
  }

  public function stop($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $invert = isset($this->configuration['invert']);
    if (!$value && !$invert || $value && $invert) {
      $message = !empty($this->configuration['message']) ? $this->configuration['message'] : NULL;
      throw new MigrateException($message);
    }
    return $value;
  }

}
