<?php

namespace Drupal\import\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\import\MigrateBatchExecutable;

class ImportDemoDataForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_demo_data_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['update'] = [
      '#type' => 'checkbox',
      '#title' => t('Update'),
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Install demo data'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $demo_data_info = \Drupal::moduleHandler()->invokeAll('demo_data_info');
    $options = [
      'limit' => 0,
      'update' => $form_state->getValue('update'),
      'force' => 0,
    ];
    $migration_plugin_manager = \Drupal::service('plugin.manager.migration');
    $message = new MigrateMessage();
    foreach ($demo_data_info as $info) {
      $migration = $migration_plugin_manager->createInstance($info['id'], $info);
      if ($migration) {
        $options['configuration'] = $info;
        $migration->setStatus(MigrationInterface::STATUS_IDLE);
        $executable = new MigrateBatchExecutable($migration, $message, $options);
        $executable->batchImport();
      }
    }
  }

}
