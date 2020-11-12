<?php

namespace Drupal\import\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExcelImportForm extends FormBase {

  /**
   * @var \Drupal\import\Entity\ExcelMigrateInterface
   */
  protected $excelMigrate;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'excel_import_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $excel_migrate = NULL) {

    $this->excelMigrate = $this->entityTypeManager->getStorage('excel_migrate')
      ->load($excel_migrate);
    if (!$this->excelMigrate) {
      $caption = '<p>' . $this->formatPlural(1, 'You can not migrate this excel %s', '%s not exist.', ['%s' => $excel_migrate]) . '</p>';
      $form['#title'] = $this->t('Excel Migrate %s', ['%s' => $excel_migrate]);
      $form['description'] = ['#markup' => $caption];
      return $form;
    }
    $validators = [
      'file_validate_extensions' => ['xls xlsx'],
      'file_validate_size' => [file_upload_max_size()],
    ];
    $form['file'] = [
      '#type' => 'file',
      '#title' => $this->t('Excel file'),
      '#description' => [
        '#theme' => 'file_upload_help',
        '#description' => $this->t('The import file which needs import.'),
        '#upload_validators' => $validators,
      ],
      '#size' => 50,
      '#upload_validators' => $validators,
      '#upload_location' => 'private://import',
    ];

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
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $this->file = _file_save_upload_from_form($form['file'], $form_state, 0);

    // Ensure we have the file uploaded.
    if (!$this->file) {
      $form_state->setErrorByName('file', $this->t('File to import not found.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $session = \Drupal::request()->getSession();
    $source = $this->excelMigrate->getSource();
    $source['path'] = $this->file->getFileUri();
    $session->set('import.excel_import_form.source', $source);
    $form_state->setRedirect('import.excel_import_form.select_sheet', [
      'excel_migrate' => $this->excelMigrate->id(),
    ]);
  }

}
