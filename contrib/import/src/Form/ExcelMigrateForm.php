<?php

namespace Drupal\import\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ExcelMigrateForm.
 */
class ExcelMigrateForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $excel_migrate = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $excel_migrate->label(),
      '#description' => $this->t("Label for the Excel migrate."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $excel_migrate->id(),
      '#machine_name' => [
        'exists' => '\Drupal\import\Entity\ExcelMigrate::load',
      ],
      '#disabled' => !$excel_migrate->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $excel_migrate = $this->entity;
    $status = $excel_migrate->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Excel migrate.', [
          '%label' => $excel_migrate->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Excel migrate.', [
          '%label' => $excel_migrate->label(),
        ]));
    }
    $form_state->setRedirectUrl($excel_migrate->toUrl('collection'));
  }

}
