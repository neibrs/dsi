<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\migrate\process\DefaultValue as DefaultValueBase;
use Drupal\migrate\Row;

class DefaultValue extends DefaultValueBase {
  
  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Gets the source value.
    if (!empty($this->configuration['default_reference_value'])) {
      $reference_value = $this->configuration['default_reference_value'];
      $migrate_plugin_manager = \Drupal::service('plugin.manager.migrate.process');
      $getProcessPlugin = $migrate_plugin_manager->createInstance('get', ['source' => $reference_value]);
      $new_value = $getProcessPlugin->transform(NULL, $migrate_executable, $row, $reference_value);
    }
    else {
      $new_value = $this->configuration['default_value'];
    }
  
    if (!empty($this->configuration['strict'])) {
      return isset($value) ? $value : $new_value;
    }
    return $value ?: $new_value;
  }
}