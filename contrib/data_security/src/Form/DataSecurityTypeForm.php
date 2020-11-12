<?php

namespace Drupal\data_security\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DataSecurityTypeForm.
 */
class DataSecurityTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $data_security_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $data_security_type->label(),
      '#description' => $this->t("Label for the Data security type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $data_security_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\data_security\Entity\DataSecurityType::load',
      ],
      '#disabled' => !$data_security_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $data_security_type = $this->entity;
    $status = $data_security_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Data security type.', [
          '%label' => $data_security_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Data security type.', [
          '%label' => $data_security_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($data_security_type->toUrl('collection'));
  }

}
