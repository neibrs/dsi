<?php

namespace Drupal\dsi_import\Form;

use Drupal\Component\Utility\Environment;
use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\dsi_import\MigrateBatchExecutable;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ImportForm extends ImportFormBase {

  protected $entity_type_id;

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return 'dsi_import_form';
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL) {
    $this->entity_type_id = $entity_type_id;

    $form_state->set('entity_type_id', $entity_type_id);

    $definitions = $this->migrationPluginManager->getDefinitions();
    $options = [];
    foreach ($definitions as $id => $definition) {
      if (isset($definition['destination']) && $definition['destination']['plugin'] == "entity:$entity_type_id") {
        $options[$id] = $this->t($definition['label']);
      }
    }
    if (empty($options)) {
      \Drupal::messenger()->addMessage($this->t('Import template not found.'), 'warning');
      return new RedirectResponse(Url::fromRoute("entity.$entity_type_id.collection")->toString());
    }
    $form['migration'] = [
      '#type' => 'select',
      '#title' => $this->t('Template'),
      '#required' => TRUE,
      '#options' => $options,
      '#ajax' => [
        'callback' => '::migrationSwitch',
        'wrapper' => 'file-wrapper',
      ],
      '#weight' => -10,
    ];

    if (!empty($options)) {
      $default_option_keys = array_keys($options);
      $form['migration']['#default_value'] = reset($default_option_keys);
    }
    $validators = [
      'file_validate_extensions' => ['xls xlsx'],
      'file_validate_size' => [Environment::getUploadMaxSize()],
    ];
    $form['file'] = [
      '#type' => 'file',
      '#title' => $this->t('Data file'),
      '#description' => [
        '#theme' => 'file_upload_help',
        '#description' => $this->t('A import file.'),
        '#upload_validators' => $validators,
      ],
      '#size' => 50,
      '#upload_validators' => $validators,
      '#upload_location' => 'private://import',
      '#prefix' => '<div id="file-wrapper">',
      '#suffix' => '</div>',
    ];

    if ($migration_id = $form_state->getValue('migration')) {
      /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
      $migration = $this->migrationPluginManager->createInstance($migration_id);
      $validators = $form['file']['#upload_validators'];
      switch ($migration->getSourceConfiguration()['plugin']) {
        case 'csv':
          $validators['file_validate_extensions'] = ['csv'];
          break;

        case 'xls':
        case 'xls_plus':
          $validators['file_validate_extensions'] = ['xls xlsx'];
          break;
      }
      $form['file']['#upload_validators'] = $validators;
      $form['file']['#description']['#upload_validators'] = $validators;
    }

    $form['update'] = [
      '#type' => 'checkbox',
      '#title' => t('Update existing data'),
      '#weight' => 10,
      '#default_value' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Import'),
      '#button_type' => 'primary',
    ];

    // Polish style
    $form['#attributes']['class'][] = 'form-horizontal';
    $form['#theme_wrappers'] = ['form__box'];

    $form['migrations'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Type of Import'),
        $this->t('Sample File'),
        $this->t('Link to Documentation'),
        $this->t('Current Data'),
        $this->t('Operation'),
      ],
    ];
    foreach ($definitions as $id => $definition) {
      $form['migrations'][$id]['label'] = [
        '#markup' => $definition['label'],
      ];
      $form['migrations'][$id]['file'] = [
        '#markup' => $this->t('Download Sample Excel File'),
      ];
      $form['migrations'][$id]['documentation'] = [
        '#markup' => $this->t('View Documentation'),
      ];
      $form['migrations'][$id]['data'] = [
        '#markup' => $this->t('	Download Current Data'),
      ];
      $form['migrations'][$id]['operation'] = [
        '#markup' => $this->t('Import'),
      ];
    }

    // 因下载模板页面报错，暂时取消下载功能 [#113]
    //      $form['file']['#description']['#description'] = $this->t('A import file. %template', [
    //        '%template' => Link::createFromRoute($this->t('Download template'), 'import.entity_import.template', [
    //          'entity_type_id' => $this->entity_type_id,
    //          'migration_id' => $migration_id,
    //        ])->toString(),
    //      ]);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $this->file = _file_save_upload_from_form($form['file'], $form_state, FileSystemInterface::EXISTS_RENAME);

    // Ensure we have the file uploaded.
    if (!$this->file) {
      $form_state->setErrorByName('file', $this->t('File to import not found.'));
    }
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
    $configuration['source']['path'] = $this->file->getFileUri();
    $configuration['source']['entity_type_id'] = $this->entity_type_id;
    if ($organization = $form_state->getValue('organization')) {
      $configuration['source']['multiple_organization'] = $organization;
    }
    $options = [
      'limit' => 0,
      'update' => $form_state->getValue('update'),
      'force' => 0,
      'configuration' => $configuration,
    ];

    $this->doMigrate($options, $form_state);
  }

  protected function doMigrate($options, FormStateInterface $form_state) {
    $migration_id = $form_state->getValue('migration');

    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    $migration = $this->migrationPluginManager->createInstance($migration_id, $options['configuration']);

    // MigrateBatchExecutable will recreate the Migration object from $migration->id()
    $migrateMessage = new MigrateMessage();
    $migration->setStatus(MigrationInterface::STATUS_IDLE);
    $executable = new MigrateBatchExecutable($migration, $migrateMessage, $options);
    $executable->batchImport();
  }

}
