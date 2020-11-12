<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * @MigrateProcessPlugin(
 *   id = "entity_field",
 * )
 */
class EntityField extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if ($entity = \Drupal::entityTypeManager()->getStorage($this->configuration['entity_type'])->load($value)) {
      $field_name= $this->configuration['field_name'];
      // TODO: support value property
      return $entity->$field_name->target_id;
    }
  }

}