<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\migrate\MigrateException;

trait EntityMigrationConditionTrait {

  protected function query($value) {

    // Entity queries typically are case-insensitive. Therefore, we need to
    // handle case sensitive filtering as a post-query step. By default, it
    // filters case insensitive. Change to true if that is not the desired
    // outcome.
    $ignoreCase = !empty($this->configuration['ignore_case']) ?: FALSE;

    $multiple = is_array($value);

    $query = \Drupal::entityTypeManager()->getStorage($this->lookupEntityType)
      ->getQuery()
      ->condition($this->lookupValueKey, $value, $multiple ? 'IN' : NULL);

    if ($this->lookupBundleKey) {
      $query->condition($this->lookupBundleKey, $this->lookupBundle);
    }

    if (isset($this->configuration['conditions'])) {
      // Plus
      $migrate_plugin_manager = \Drupal::service('plugin.manager.migrate.process');
      foreach ($this->configuration['conditions'] as $key => $condition) {
        $getProcessPlugin = $migrate_plugin_manager->createInstance('get', ['source' => $condition]);
        $condition = $getProcessPlugin->transform(NULL, $this->migrateExecutable, $this->row, $condition);
        if(!empty($condition)) {
          $query->condition($key, $condition);
        }
      }
    }

    $results = $query->execute();

    if (empty($results)) {
      if (!empty($this->configuration['not_found_message'])) {
        $this->messenger()->addWarning($value . ': ' . $this->configuration['not_found_message']);
        if(empty($this->configuration['process'])) {
          throw new MigrateException($value . ': ' . $this->configuration['not_found_message']);
        }
      }

      return NULL;
    }

    // By default do a case-sensitive comparison.
    if (!$ignoreCase) {
      // Returns the entity's identifier.
      foreach ($results as $k => $identifier) {
        $entity = \Drupal::entityTypeManager()->getStorage($this->lookupEntityType)->load($identifier);
        $result_value = $entity instanceof ConfigEntityInterface ? $entity->get($this->lookupValueKey) : $entity->get($this->lookupValueKey)->value;
        if (($multiple && !in_array($result_value, $value, TRUE)) || (!$multiple && $result_value !== $value)) {
          unset($results[$k]);
        }
      }
    }

    if ($multiple && !empty($this->destinationProperty)) {
      array_walk($results, function (&$value) {
        $value = [$this->destinationProperty => $value];
      });
    }


    return $multiple ? array_values($results) : reset($results);
  }
}
