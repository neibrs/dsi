<?php

namespace Drupal\import\Plugin\migrate\process;

use Drupal\Core\Entity\EntityManagerInterface;
use Drupal\Core\Entity\EntityReferenceSelection\SelectionPluginManagerInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Plugin\MigratePluginManager;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_plus\Plugin\migrate\process\EntityLookup;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @MigrateProcessPlugin(
 *   id = "entity_lookup_plus",
 *   handle_multiples = TRUE
 * )
 */
class EntityLookupPlus extends EntityLookup {

  use EntityMigrationConditionTrait;

  /**
   * The row from the source to process.
   *
   * @var \Drupal\migrate\Row
   */
  protected $row;

  /**
   * The MigrateExecutable instance.
   *
   * @var \Drupal\migrate\MigrateExecutable
   */
  protected $migrateExecutable;

  /**
   * The MigratePluginManager instance.
   *
   * @var \Drupal\migrate\Plugin\MigratePluginManagerInterface
   */
  protected $processPluginManager;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $pluginId, $pluginDefinition, MigrationInterface $migration = NULL) {
    $instance = parent::create($container, $configuration, $pluginId, $pluginDefinition, $migration);
    $instance->processPluginManager = $container->get('plugin.manager.migrate.process');
    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrateExecutable, Row $row, $destinationProperty){
    /**
     * 在 EntityMigrationConditionTrait::query() 中调用 get 插件的 transform 需要用到 $migrateExecutable 和 $row.
     *
     * @see \Drupal\import\Plugin\migrate\process\EntityMigrationConditionTrait::query()
     * @see \Drupal\migrate_plus\Plugin\migrate\process\EntityGenerate::transform()
     */
    $this->row = $row;
    $this->migrateExecutable = $migrateExecutable;
    $result = parent::transform($value, $migrateExecutable, $row, $destinationProperty);

    return $result;
  }
}
