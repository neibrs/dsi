<?php

namespace Drupal\import;

use Drupal\migrate\MigrateExecutable;
use Drupal\migrate\Plugin\MigrationInterface;

class ImportManager implements ImportManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function doMigrates($configurations) {
    foreach ($configurations as $key => $configuration) {
      $migration = \Drupal::service('plugin.manager.migration')
        ->createInstance($key, $configuration);
      $migration->getIdMap()->prepareUpdate();
      $migration->setStatus(MigrationInterface::STATUS_IDLE);
      $executable = new MigrateExecutable($migration);

      \Drupal::logger('import')->notice('Import %migration', ['%migration' => $key]);

      $executable->import();
    }
  }

}