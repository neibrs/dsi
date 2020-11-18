<?php

namespace Drupal\dsi_client\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ClientTypeForm.
 */
class ClientTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $dsi_client_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dsi_client_type->label(),
      '#description' => $this->t("Label for the Client type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dsi_client_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dsi_client\Entity\ClientType::load',
      ],
      '#disabled' => !$dsi_client_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dsi_client_type = $this->entity;
    $status = $dsi_client_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Client type.', [
          '%label' => $dsi_client_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Client type.', [
          '%label' => $dsi_client_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($dsi_client_type->toUrl('collection'));
  }

}
