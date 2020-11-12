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

  use EntityLookupTrait;

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

}
