<?php

namespace Drupal\person\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "person_lookup",
 * )
 */
class PersonLookup extends ProcessPluginBase {

  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    $storage = \Drupal::entityTypeManager()->getStorage('person');
    if ($this->configuration['number_source'] && !empty($row->getSourceProperty($this->configuration['number_source']))) {
      $number = $row->get($this->configuration['number_source']);
      if (!empty($number)) {
        $entities = $storage->loadByProperties(['number' => trim($number)]);
        if (!empty($entities)) {
          return reset(array_keys($entities));
        }
      }
  
      $this->messenger()->addWarning($this->configuration['not_found_message'] . ': ' . $number . ' - ' . $value);
      
      throw new MigrateSkipRowException($this->configuration['not_found_message'] . ': ' . $value);
    }

    $entities = $storage->loadByProperties(['name' => trim($value)]);
    $id = reset(array_keys($entities));
    if (empty($id) && isset($this->configuration['not_found_message'])) {
      $this->messenger()->addWarning($value . ': ' . $this->configuration['not_found_message']);
      
      throw new MigrateSkipRowException($this->configuration['not_found_message'] . ': ' . $value);
    }

    return $id;
  }

}
