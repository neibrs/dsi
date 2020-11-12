<?php

namespace Drupal\import;

use Drupal\migrate\MigrateException;
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
        'finished' => '\Drupal\import\MigrateBatchExecutable::batchFinishedImport',
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
        '\Drupal\import\MigrateBatchExecutable::batchProcessImport',
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
   * Batch 'operation' callback.
   *
   * @param string $migration_id
   *   The migration id.
   * @param array $options
   *   The batch executable options.
   * @param array $context
   *   The sandbox context.
   */
  public static function batchProcessImport($migration_id, array $options, &$context) {
    if (empty($context['sandbox'])) {
      $context['finished'] = 0;
      $context['sandbox'] = [];
      $context['sandbox']['total'] = 0;
      $context['sandbox']['counter'] = 0;
      $context['sandbox']['batch_limit'] = 0;
      $context['sandbox']['operation'] = MigrateBatchExecutable::BATCH_IMPORT;
    }

    // Prepare the migration executable.
    $message = new MigrateMessage();
    $configuration = [];
    if (isset($options['configuration'])) {
      $configuration = $options['configuration'];
    }
    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    $migration = \Drupal::getContainer()->get('plugin.manager.migration')->createInstance($migration_id, $configuration);

    // Fix: 如果在selectSheet页面取消字段的匹配，不应该清除字段的值。
    // 解决方案，如果取消字段匹配，就删除相应的　process。
    // 1. 找到不处理的 columns
    $migration_source = $migration->getSourceConfiguration();
    // 比如获取person_xls的source
    $origin_source_rows =static::getSourceRows($migration_source['columns']);

    // User selected columns.
    $source_rows = static::getSourceRows($options['configuration']['source']['columns']);

    // 找到原始migration定义的栏目，并且是用户未选择的
    $diff_source_rows = array_diff($origin_source_rows, $source_rows);

    // 2. 删除多余的process
    $process = $migration->getProcess();
    // Remove unmatched process
    foreach ($process as $key => $value) {
      if (isset($value['source'])) {
        $source = $value['source'];
      }
      elseif (isset($value[0]['source'])) {
        $source = $value[0]['source'];
      }
      else {
        throw new MigrateException('Source not found.');
      }

      if (in_array($source, $diff_source_rows)) {
        unset($process[$key]);
      }
    }
    $migration->setProcess($process);

    // Each batch run we need to reinitialize the counter for the migration.
    if (!empty($options['limit']) && isset($context['results'][$migration->id()]['@numitems'])) {
      $options['limit'] = $options['limit'] - $context['results'][$migration->id()]['@numitems'];
    }

    $executable = new MigrateBatchExecutable($migration, $message, $options);

    if (empty($context['sandbox']['total'])) {
      $context['sandbox']['total'] = $executable->getSource()->count();
      $context['sandbox']['batch_limit'] = $executable->calculateBatchLimit($context);
      $context['results'][$migration->id()] = [
        '@numitems' => 0,
        '@created' => 0,
        '@updated' => 0,
        '@failures' => 0,
        '@ignored' => 0,
        '@name' => $migration->id(),
      ];
    }

    // Every iteration, we reset out batch counter.
    $context['sandbox']['batch_counter'] = 0;

    // Make sure we know our batch context.
    $executable->setBatchContext($context);

    // Do the import.
    $result = $executable->import();

    // Store the result; will need to combine the results of all our iterations.
    $context['results'][$migration->id()] = [
      '@numitems' => $context['results'][$migration->id()]['@numitems'] + $executable->getProcessedCount(),
      '@created' => $context['results'][$migration->id()]['@created'] + $executable->getCreatedCount(),
      '@updated' => $context['results'][$migration->id()]['@updated'] + $executable->getUpdatedCount(),
      '@failures' => $context['results'][$migration->id()]['@failures'] + $executable->getFailedCount(),
      '@ignored' => $context['results'][$migration->id()]['@ignored'] + $executable->getIgnoredCount(),
      '@name' => $migration->id(),
    ];

    // Do some housekeeping.
    if (
      $result != MigrationInterface::RESULT_INCOMPLETE
    ) {
      $context['finished'] = 1;
    }
    else {
      $context['sandbox']['counter'] = $context['results'][$migration->id()]['@numitems'];
      if ($context['sandbox']['counter'] <= $context['sandbox']['total']) {
        $context['finished'] = ((float) $context['sandbox']['counter'] / (float) $context['sandbox']['total']);
        $context['message'] = t('Importing %migration (@percent%).', [
          '%migration' => $migration->label(),
          '@percent' => (int) ($context['finished'] * 100),
        ]);
      }
    }

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
