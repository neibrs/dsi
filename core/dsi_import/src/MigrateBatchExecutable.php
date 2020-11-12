<?php

namespace Drupal\dsi_import;

use Drupal\migrate\MigrateMessageInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_tools\MigrateBatchExecutable as MigrateBatchExecutableBase;

/**
 * Provide configuration support for migrate_tools
 */
class MigrateBatchExecutable extends MigrateBatchExecutableBase {

  protected $configuration;

  /**
   * {@inheritdoc}
   */
  public function __construct(MigrationInterface $migration, MigrateMessageInterface $message, array $options) {
    parent::__construct($migration, $message, $options);

    if (isset($options['configuration'])) {
      $this->configuration = $options['configuration'];
    }
  }

  /**
   * Setup batch operations for running the migration.
   */
  public function batchImport() {
    // Create the batch operations for each migration that needs to be executed.
    // This includes the migration for this executable, but also the dependent
    // migrations.
    $operations = $this->batchOperations([$this->migration], 'import', [
      'limit' => $this->itemLimit,
      'update' => $this->updateExistingRows,
      'force' => $this->checkDependencies,
      'configuration' => $this->configuration,
    ]);

    if (count($operations) > 0) {
      $batch = [
        'operations' => $operations,
        'title' => t('Migrating %migrate', ['%migrate' => $this->migration->label()]),
        'init_message' => t('Start migrating %migrate', ['%migrate' => $this->migration->label()]),
        'progress_message' => t('Migrating %migrate', ['%migrate' => $this->migration->label()]),
        'error_message' => t('An error occurred while migrating %migrate.', ['%migrate' => $this->migration->label()]),
        'finished' => '\Drupal\dsi_import\MigrateBatchExecutable::batchFinishedImport',
      ];

      batch_set($batch);
    }
  }

  /**
   * Helper to generate the batch operations for importing migrations.
   *
   * @param \Drupal\migrate\Plugin\MigrationInterface[] $migrations
   *   The migrations.
   * @param string $operation
   *   The batch operation to perform.
   * @param array $options
   *   The migration options.
   *
   * @return array
   *   The batch operations to perform.
   */
  protected function batchOperations(array $migrations, $operation, array $options = []) {
    $operations = [];
    foreach ($migrations as $id => $migration) {

      if (!empty($options['update'])) {
        $migration->getIdMap()->prepareUpdate();
      }

      if (!empty($options['force'])) {
        $migration->set('requirements', []);
      }
      else {
        $dependencies = $migration->getMigrationDependencies();
        if (!empty($dependencies['required'])) {
          $required_migrations = $this->migrationPluginManager->createInstances($dependencies['required']);
          // For dependent migrations will need to be migrate all items.
          $dependent_options = $options;
          $dependent_options['limit'] = 0;
          $operations += $this->batchOperations($required_migrations, $operation, [
            'limit' => 0,
            'update' => $options['update'],
            'force' => $options['force'],
          ]);
        }
      }

      $operations[] = [
        '\Drupal\migrate_tools\MigrateBatchExecutable::batchProcessImport',
        [$migration->id(), $options],
      ];
    }

    return $operations;
  }

  public static function getSourceRows($columns) {
    $source_rows = [];
    foreach ($columns as $rows) {
      $rows_values = array_values($rows);
      $source_rows[] = $rows_values[0];
    }
    return $source_rows;
  }

  /**
   * {@inheritdoc}
   * Override translation strings.
   */
  public static function batchFinishedImport($success, array $results, array $operations) {
    if ($success) {
      foreach ($results as $migration_id => $result) {
        $singular_message = t("Processed 1 item (@created created, @updated updated, @failures failed, @ignored ignored) - done with '@name'");
        $plural_message = t("Processed @numitems items (@created created, @updated updated, @failures failed, @ignored ignored) - done with '@name'");
        \Drupal::messenger()->addStatus(\Drupal::translation()->formatPlural($result['@numitems'],
          $singular_message,
          $plural_message,
          $result));
      }
    }
  }

  /**
   * {@inheritdoc}
   * Override translation strings.
   */
  protected function progressMessage($done = TRUE) {
    $processed = $this->getProcessedCount();
    if ($done) {
      $singular_message = t("Processed 1 item (@created created, @updated updated, @failures failed, @ignored ignored) - done with '@name'");
      $plural_message = t("Processed @numitems items (@created created, @updated updated, @failures failed, @ignored ignored) - done with '@name'");
    }
    else {
      $singular_message = t("Processed 1 item (@created created, @updated updated, @failures failed, @ignored ignored) - continuing with '@name'");
      $plural_message = t("Processed @numitems items (@created created, @updated updated, @failures failed, @ignored ignored) - continuing with '@name'");
    }
    $this->message->display(\Drupal::translation()->formatPlural($processed,
      $singular_message, $plural_message,
      [
        '@numitems' => $processed,
        '@created' => $this->getCreatedCount(),
        '@updated' => $this->getUpdatedCount(),
        '@failures' => $this->getFailedCount(),
        '@ignored' => $this->getIgnoredCount(),
        '@name' => $this->migration->id(),
      ]
    ));
  }

}
