<?php

namespace Drupal\person\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\import\MigrateBatchExecutable;
use Drupal\import\Form\ImportForm;

class PersonImportForm extends ImportForm {

  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL) {
    $form = parent::buildForm($form, $form_state, $entity_type_id);

    $form['migration']['#default_value'] = 'person_xls';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function doMigrate($options, FormStateInterface $form_state) {
    $migration_id = $form_state->getValue('migration');

    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    $migration = $this->migrationPluginManager->createInstance($migration_id, $options['configuration']);
    $source_configuration = $migration->getSourceConfiguration();
    if (isset($source_configuration['sheet_name'])) {
      \Drupal::service('tempstore.private')->get('import_entity.import_form.options')->set($migration_id, $options);

      $options = [];
      if ($destination = $this->getRequest()->query->get('destination')) {
        $this->getRequest()->query->remove('destination');
        $options['query']['destination'] = $destination;
      }

      $form_state->setRedirect('person.entity_import.select_sheet', [
        'migration_id' => $migration_id,
      ], $options);
      return;
    }

    // MigrateBatchExecutable will recreate the Migration object from $migration->id()
    $migrateMessage = new MigrateMessage();
    $migration->setStatus(MigrationInterface::STATUS_IDLE);
    $executable = new MigrateBatchExecutable($migration, $migrateMessage, $options);
    $executable->batchImport();
  }
}