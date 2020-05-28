<?php

namespace Drupal\excel_export\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ExcelExportForm.
 */
class ExcelExportForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $excel_export = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $excel_export->label(),
      '#description' => $this->t("Label for the Excel export."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $excel_export->id(),
      '#machine_name' => [
        'exists' => '\Drupal\excel_export\Entity\ExcelExport::load',
      ],
      '#disabled' => !$excel_export->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $excel_export = $this->entity;
    $status = $excel_export->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Excel export.', [
          '%label' => $excel_export->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Excel export.', [
          '%label' => $excel_export->label(),
        ]));
    }
    $form_state->setRedirectUrl($excel_export->toUrl('collection'));
  }

}
