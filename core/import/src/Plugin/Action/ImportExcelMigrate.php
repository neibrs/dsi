<?php

namespace Drupal\import\Plugin\Action;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Action\ConfigurableActionBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use Drupal\import\MigrateBatchExecutable;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * @Action(
 *   id = "excel_migrate_import",
 *   label = @Translation("Import excel file"),
 *   type = "excel_migrate"
 * )
 */
class ImportExcelMigrate extends ConfigurableActionBase implements ContainerFactoryPluginInterface {

  /**
   * @var \Drupal\migrate\Plugin\MigrationPluginManagerInterface
   */
  protected $migrationPluginManager;

  public function __construct($configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager, MigrationPluginManagerInterface $migration_plugin_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $entity_type_manager);

    $this->migrationPluginManager = $migration_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.migration')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'update' => FALSE,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['update'] = [
      '#type' => 'checkbox',
      '#title' => t('Update'),
      '#default_value' => $this->configuration['update'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $this->configuration['update'] = $form_state->getValue('update');
  }

  /**
   * {@inheritdoc}
   */
  public function access($object, AccountInterface $account = NULL, $return_as_object = FALSE) {
    return $return_as_object ? AccessResult::allowed() : TRUE;
  }

  /**
   * {@inheritdoc}
   * @throws \Drupal\migrate\MigrateException
   */
  public function execute($entity = NULL) {
    foreach ($entity->getSheets() as $key => $ids) {
      foreach ($ids as $id) {
        if (empty($id) || $id === 0) {
          continue;
        }
        $configuration = [
          'source' => $entity->getSource(),
        ];
        $configuration['source']['sheet_name'] = $key;

        /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
        $migration = $this->migrationPluginManager->createInstance($id, $configuration);

        // MigrateBatchExecutable will recreate the Migration object from $migration->id()
        $migrateMessage = new MigrateMessage();
        $options = [
          'limit' => 0,
          'update' => $this->configuration['update'],
          'force' => 0,
          'configuration' => $configuration,
        ];
        $executable = new MigrateBatchExecutable($migration, $migrateMessage, $options);
        $executable->batchImport();
      }
    }
  }

}
