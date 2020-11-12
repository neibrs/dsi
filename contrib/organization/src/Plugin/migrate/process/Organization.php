<?php

namespace Drupal\organization\Plugin\migrate\process;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate\Plugin\MigratePluginManager;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a 'Organization' migrate process plugin.
 *
 * Examples:
 *
 * @code
 * process:
 *   destination_field:
 *     plugin: organization
 *     source: source_field
 *     delimiter: _
 *     default_values:
 *       type: department
 *     values:
 *       type: constants/organization_type
 *       classification: '@classification'
 *
 * @MigrateProcessPlugin(
 *  id = "organization"
 * )
 */
class Organization extends ProcessPluginBase implements ContainerFactoryPluginInterface {
  /**
   * The MigratePluginManager instance.
   *
   * @var \Drupal\migrate\Plugin\MigratePluginManagerInterface
   */
  protected $migratePluginManager;

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
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigratePluginManager $migrate_plugin_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->migratePluginManager = $migrate_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.migrate.process')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (empty($value)) {
      return NULL;
    }

    $delimiter = '-';
    if (isset($this->configuration['delimiter'])) {
      $delimiter = $this->configuration['delimiter'];
    }

    /** @var \Drupal\organization\OrganizationStorageInterface $organization_storage */
    $organization_storage = \Drupal::entityTypeManager()->getStorage('organization');
    $names = explode($delimiter, $value);
    $parent = NULL;
    foreach ($names as $name) {
      $entity_values = [];

      $name_or_description = $name;

      if ($parent) {
        $entity_values['parent'] = $parent->id();
      }

      // 处理 default_values.
      if (isset($this->configuration['default_values']) && is_array($this->configuration['default_values'])) {
        foreach ($this->configuration['default_values'] as $key => $default_value) {
          $entity_values[$key] = $default_value;
        }
      }
      // 处理 values.
      if (isset($this->configuration['values']) && is_array($this->configuration['values'])) {
        foreach ($this->configuration['values'] as $key => $property) {
          $getProcessPlugin = $this->migratePluginManager->createInstance('get', ['source' => $property]);
          $source_value = $getProcessPlugin->transform(NULL, $migrate_executable, $row, $property);
          $entity_values[$key] = $source_value;
        }
      }

      $query = $organization_storage->getQuery();
      $query->condition($query->orConditionGroup()
        ->condition('name', $name_or_description)
        ->condition('description', $name_or_description)
      );
      foreach ($entity_values as $key => $entity_value) {
        $query->condition($key, $entity_value);
      }
      $ids = $query->execute();
      if ($id = reset($ids)) {
        $organization = $parent = $organization_storage->load($id);
      }
    }

    if (!isset($organization) && !empty($this->configuration['not_found_message'])) {
      $this->messenger()->addWarning($value . ': ' . $this->configuration['not_found_message']);
      throw new MigrateSkipRowException($value . ': ' . $this->configuration['not_found_message']);
    }

    if (isset($organization)) {
      return $organization->id();
    }
  }

}
