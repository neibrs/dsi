<?php

namespace Drupal\dsi_device\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DeviceTypeForm.
 */
class DeviceTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $dsi_device_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dsi_device_type->label(),
      '#description' => $this->t("Label for the Device type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dsi_device_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dsi_device\Entity\DeviceType::load',
      ],
      '#disabled' => !$dsi_device_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dsi_device_type = $this->entity;
    $status = $dsi_device_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Device type.', [
          '%label' => $dsi_device_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Device type.', [
          '%label' => $dsi_device_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($dsi_device_type->toUrl('collection'));
  }

}
