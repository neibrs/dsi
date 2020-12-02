<?php

namespace Drupal\import\Form;

use Drupal\Component\Utility\Environment;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\import\MigrateBatchExecutable;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ImportForm extends ImportFormBase {

  protected $entity_type_id;

  /** @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface $form_display */
  protected $form_display;

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_form';
  }

  /**
   * {@inheritdoc}
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
      drupal_set_message($this->t('Import template not found.'), 'warning');
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

    $multiple_organization_classification = \Drupal::entityTypeManager()->getDefinition($entity_type_id)->get('multiple_organization_classification');

    if ($multiple_organization_classification && \Drupal::moduleHandler()->moduleExists('person')) {
      $organizations = \Drupal::service('person.manager')->currentPersonAccessibleOrganizationByClassification($multiple_organization_classification);

      $options = array_map(function ($entity) {
        return $entity->label();
      }, $organizations );

      $form['organization'] = [
        '#title' => $this->t($multiple_organization_classification),
        '#type' => 'select',
        '#options' => $options,
        '#required' => FALSE,
      ];
    }

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
    if (!empty($default_option_keys)) {
      /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
      $migration = $this->migrationPluginManager->createInstance(reset($default_option_keys));
    }

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

    // 将 default_value_fields 放到表单里让用户设置这些字段的导入默认值.
    $default_value_fields = [];
    if (isset($migration)) {
      $destination = $migration->getDestinationConfiguration('destination');
      if (isset($destination['default_value_fields'])) {
        $default_value_fields = $destination['default_value_fields'];
      }
    }
    if (empty($default_value_fields)) {
      $settings = \Drupal::config('import.settings')->get('default_value_fields');
      if (isset($settings[$entity_type_id])) {
        $default_value_fields = $settings[$entity_type_id];
      }
    }
    if (!empty($default_value_fields)) {
      $this->default_value_fields = $default_value_fields;
      $form['default_value_fields'] = [
        '#type' => 'fieldset',
        '#title' => $this->t('Default value'),
        '#weight' => 20,
        '#prefix' => '<div id="default-value-fields-wrapper">',
        '#suffix' => '</div>',
      ];

      // 为 EntityFormDisplay 准备 entity.
      $values = [];
      // 如果实体有bundle，需要在创建实体时提供bundle。
      $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);
      if ($bundle_entity_type = $entity_type->getBundleEntityType()) {
        $entities = \Drupal::entityTypeManager()->getStorage($bundle_entity_type)->loadByProperties([
          'status' => true,
        ]);
        $keys = array_keys($entities);
        $values[$entity_type->getKey('bundle')] = reset($keys);
      }
      $this->entity = \Drupal::entityTypeManager()->getStorage($entity_type_id)->create($values);

      // 通过 EntityFormDisplay 获得字段的widget并放到表单中.
      $display = EntityFormDisplay::collectRenderDisplay($this->entity, 'default');
      foreach ($display->getComponents() as $key => $component) {
        if (!in_array($key, $this->default_value_fields)) {
          $display->removeComponent($key);
        }
      }
      $display->buildForm($this->entity, $form['default_value_fields'], $form_state);

      $this->form_display = $display;
    }

    // Polish style
    $form['#attributes']['class'][] = 'form-horizontal';
    $form['#theme_wrappers'] = ['form__box'];

    return $form;
  }

  /**
   * AJAX callback.
   */
  public function migrationSwitch($form, FormStateInterface $form_state) {
    $response = new AjaxResponse();

    $response->addCommand(new ReplaceCommand('#file-wrapper', $form['file']));
    $response->addCommand(new ReplaceCommand('#default-value-fields-wrapper', $form['default_value_fields']));

    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $this->file = _file_save_upload_from_form($form['file'], $form_state, FILE_EXISTS_RENAME);

    // Ensure we have the file uploaded.
    if (!$this->file) {
      $form_state->setErrorByName('file', $this->t('File to import not found.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
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

    // Default value fields.
    if ($this->form_display) {
      $this->form_display->extractFormValues($this->entity, $form['default_value_fields'], $form_state);
      $field_definitions = \Drupal::service('entity_field.manager')->getFieldStorageDefinitions($this->entity_type_id);
      foreach ($this->default_value_fields as $field_name) {
        $value = $form_state->getValue($field_name);
        if (empty($value)) {
          continue;
        }

        switch($field_definitions[$field_name]->getType()) {
          case 'entity_reference':
            $options['configuration']['source'][$field_name] = $value[0]['target_id'];
            break;
          default:
            $options['configuration']['source'][$field_name] = $value;
            break;
        }
      }
    }

    $this->doMigrate($options, $form_state);
  }

  protected function doMigrate($options, FormStateInterface $form_state) {
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

      $form_state->setRedirect('import.entity_import.select_sheet', [
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
