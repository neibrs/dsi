<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\process\EntityGenerate;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plus features:
 * 1. Add conditions for entity_generate.
 * 2. Import automatically from entity field definitions.
 *
 * @MigrateProcessPlugin(
 *   id = "entity_generate_plus"
 * )
 */
class EntityGeneratePlus extends EntityGenerate {

  use EntityMigrationConditionTrait;

  /**
   * The MigrateExecutable instance.
   *
   * @var \Drupal\migrate\MigrateExecutable
   */
  protected $migrateExecutable;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition, MigrationInterface $migration = NULL) {
    $instance = parent::create($container, $configuration, $pluginId, $pluginDefinition, $migration);
    $instance->configuration['ignore_case'] = TRUE;
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  protected function entity($value) {
    $entity_values = parent::entity($value);

    $entity_type_id = $this->destinationEntityType;
    $bundle = $this->destinationBundleKey;
    if (!$bundle) {
      $bundle = $entity_type_id;
    }

    $row = $this->row;

    /** @var \Drupal\Core\Field\FieldDefinitionInterface[] $field_definitions */
    $field_definitions = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type_id, $bundle);
    foreach ($field_definitions as $field_name => $field_definition) {
      if (isset($entity_values[$field_name])) {
        continue;
      }

      // TODO: 判断字段类型进行细致的处理
      if ($value =$row->getSourceProperty($field_name)) {
        $entity_values[$field_name] = $value;
      }
      elseif ($value = $row->getSourceProperty($field_definition->getLabel())) {
        $entity_values[$field_name] = $value;
      }
    }

    return $entity_values;
  }

}
