<?php

namespace Drupal\dsi_finance\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class FinanceExpenditureTypeForm.
 */
class FinanceExpenditureTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $dsi_finance_expenditure_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dsi_finance_expenditure_type->label(),
      '#description' => $this->t("Label for the Finance expenditure type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dsi_finance_expenditure_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dsi_finance\Entity\FinanceExpenditureType::load',
      ],
      '#disabled' => !$dsi_finance_expenditure_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dsi_finance_expenditure_type = $this->entity;
    $status = $dsi_finance_expenditure_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Finance expenditure type.', [
          '%label' => $dsi_finance_expenditure_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Finance expenditure type.', [
          '%label' => $dsi_finance_expenditure_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($dsi_finance_expenditure_type->toUrl('collection'));
  }

}
