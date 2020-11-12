<?php

namespace Drupal\import\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\import\ImportTrait;
use Drupal\import\MigrateBatchExecutable;
use Drupal\migrate\MigrateMessage;
use Drupal\migrate\Plugin\MigrationInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ImportFormSelectSheet extends ImportFormBase {

  use ImportTrait;

  /**
   * @var \Drupal\migrate\Plugin\MigrationInterface
   */
  protected $migration;
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'import_form_select_sheet';
  }
 
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $migration_id = NULL) {
    $this->migration_id = $migration_id;

    $this->options = \Drupal::service('tempstore.private')->get('import_entity.import_form.options')->get($migration_id);

    $this->configuration['path'] = \Drupal::service('file_system')->realpath($this->options['configuration']['source']['path']);
    $xls = IOFactory::createReaderForFile($this->configuration['path'])->load($this->configuration['path']);

    $sheets = $xls->getAllSheets();
    $options = ['- Select -'];
    foreach ($sheets as $sheet) {
      $title = $sheet->getTitle();
      $options[$title] = $title;
    }
    $form['sheet'] = [
      '#title' => $this->t('Sheet'),
      '#type' => 'select',
      '#options' => $options,
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::sheetSwitch',
        'wrapper' => 'sources-wrapper',
      ],
    ];

    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    $this->migration = $this->migrationPluginManager->createInstance($this->migration_id, $this->options['configuration']);

    $source_configuration = $this->migration->getSourceConfiguration();
    $header_row = $source_configuration['header_row'];
    if (!$header_row) {
      $header_row = 1;
    }
    $form['header_row'] = [
      '#title' => $this->t('Header row'),
      '#type' => 'select',
      '#options' => [1, 2, 3, 4, 5],
      '#default_value' => $header_row,
      '#ajax' => [
        'callback' => '::sheetSwitch',
        'wrapper' => 'sources-wrapper',
      ],
    ];
  
    $default_sheet = $source_configuration['sheet_name'];
    if (!empty($default_sheet)) {
      $form['sheet']['#default_value'] = $default_sheet;
    }
 
    $form['sources'] = [
      '#title' => $this->t('Sources'),
      '#type' => 'table',
      '#header' => [$this->t('Data item'), $this->t('Excel column')],
      '#prefix' => '<div id="sources-wrapper">',
      '#suffix' => '</div>',
    ];

    // Provides valid options
    if ($sheet_name = $form_state->getValue('sheet')) {
      $default_sheet = $sheet_name;
    }
    if ($default_sheet) {

        $entity_type_id = $source_configuration['entity_type_id'];

        if (array_key_exists('type', $source_configuration)) {
          $bundle = $source_configuration['type'];
        }
        else {
          $bundle = $entity_type_id;
        }
        
      // 用户选了 sheet 后，才显示映射表.
      $columns = $this->getFieldConfigColumns($entity_type_id, $bundle);
      $columns = array_merge($this->getColumns(), $columns);
      foreach ($columns as $key => $value) {
        $form['sources'][$key]['source'] = [
          '#markup' => $key,
        ];

        $form['sources'][$key]['column'] = [
          '#type' => 'select',
          '#default_value' => $value,
        ];
        if (in_array($key, $source_configuration['keys'])) {
          $form['sources'][$key]['column']['#required'] = TRUE;
        }
      }

      if ($new_header_row = $form_state->getValue('header_row')) {
        $is_ajax = TRUE;
        $header_row = $new_header_row;
      }

      list($options, $column_map) = $this->getExcelColumns($xls, $default_sheet, $header_row);
      $this->excel_columns = $options;
      $this->column_map = $column_map;

      foreach ($columns as $key => $value) {
        $form['sources'][$key]['column']['#options'] = $options;
        if (isset($column_map[$value])) {
          $form['sources'][$key]['column']['#default_value'] = $column_map[$value];
          if (isset($is_ajax)) {
            $form['sources'][$key]['column']['#value'] = $column_map[$value];
          }
        }
      }
    }

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Import'),
      '#button_type' => 'primary',
    ];

    // Polish style
    $form['#attributes']['class'][] = 'form-horizontal';
    $form['#theme_wrappers'] = ['form__box'];
    
    return $form;
  }
 
  public function sheetSwitch($form, FormStateInterface $form_state) {
    return $form['sources'];
  }

  protected function getExcelColumns(Spreadsheet $xls, $sheet_name, $header_row) {
    $columns = ['' => $this->t('-Select-')];
    $column_map = [];

    try {
      $xls->setActiveSheetIndexByName($sheet_name);
    }
    catch (Exception $e) {
      return [$columns, $column_map];
    }

    $iterator = $xls->getActiveSheet()->getRowIterator($header_row, $header_row);
    foreach ($iterator->current()->getCellIterator() as $cell) {
      $column = rtrim($cell->getValue());
      if (!empty($column)) {
        $col = $cell->getColumn();
        $columns['_' . $col] = $col . '-' . $column;
        $column_map[$column] = '_' . $col;
      }
    }

    return [$columns, $column_map];
  }
 
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $configuration = $this->options['configuration'];

    if ($this->migration->getSourceConfiguration()['plugin'] == 'xls') {
      $configuration['source']['plugin'] = 'xls_plus';
    }
    $configuration['source']['sheet_name'] = $form_state->getValue('sheet');
    $configuration['source']['header_row'] = $form_state->getValue('header_row');

    // columns
    $sources = array_map(function ($item) {
      return $item['column'];
    }, $form_state->getValue('sources'));
    $columns = [];
    $i = 0;
    foreach ($sources as $key => $value) {
      if (empty($value)) {
        continue;
      }
      $columns[$i] = [$value => $key];
      $i++;
    }
    $configuration['source']['columns'] = $columns;

    $this->options['configuration'] = $configuration;

    // Recreate migration according to new configuration.
    $this->migration = $this->migrationPluginManager->createInstance($this->migration_id, $this->options['configuration']);

    $this->migration->setStatus(MigrationInterface::STATUS_IDLE);
    $migrateMessage = new MigrateMessage();
    $executable = new MigrateBatchExecutable($this->migration, $migrateMessage, $this->options);
    $executable->batchImport();
  }

  public function getMigration() {
    return $this->migration;
  }

}
