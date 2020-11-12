<?php

namespace Drupal\import\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\import\Entity\ExcelMigrateInterface;
use Drupal\migrate\Plugin\MigrationPluginManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExcelImportSelectSheetForm extends FormBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\migrate\Plugin\MigrationPluginManagerInterface
   */
  protected $migrationPluginManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, MigrationPluginManagerInterface $migration_plugin_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->migrationPluginManager = $migration_plugin_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('plugin.manager.migration')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'excel_import_select_sheet_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ExcelMigrateInterface $excel_migrate = NULL) {
    $this->excelMigrate = $excel_migrate;

    $session = \Drupal::request()->getSession();
    $source = $session->get('import.excel_import_form.source');
    $this->excelMigrate->setSource($source);

    $excel_migrations = [$this->t('- Select -')];
    $definitions = $this->migrationPluginManager->getDefinitions();
    foreach ($definitions as $id => $definition) {
      if (in_array($definition['source']['plugin'], ['xls', 'xls_plus'])) {
        $excel_migrations[$id] = $definition['label'];
      }
    }

    $form['sheets'] = [
      '#type' => 'table',
      '#header' => [$this->t('Sheet'), $this->t('Migration')],
    ];

    $xls = IOFactory::createReaderForFile(drupal_realpath($source['path']))->load(drupal_realpath($source['path']));
    $sheets = $xls->getAllSheets();
    foreach ($sheets as $sheet) {
      $title = $sheet->getTitle();

      $form['sheets'][$title]['name'] = ['#markup' => $title];

      $default_migration = $this->excelMigrate->getSheets()[$title];
      if (is_array($default_migration)) {
        $default_migration = reset($default_migration);
      }
      $form['sheets'][$title]['migration'] = [
        '#type' => 'select',
        '#default_value' => $default_migration,
        '#options' => $excel_migrations,
      ];
    }

    $form['update'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Update'),
      '#default_value' => FALSE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Import'),
      '#button_type' => 'primary',
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->excelMigrate->setSheets($form_state->getValue('sheets'));

    /** @var \Drupal\system\ActionConfigEntityInterface $import */
    $import = $this->entityTypeManager->getStorage('action')
      ->load('excel_migrate_import');
    $configuration = $import->get('configuration');
    $configuration['update'] = $form_state->getValue('update');
    $import->set('configuration', $configuration);

    $import->execute([$this->excelMigrate]);

    drupal_set_message(t('Import successed.'));
  }

}
