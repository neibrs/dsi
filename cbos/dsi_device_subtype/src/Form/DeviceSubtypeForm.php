<?php

namespace Drupal\dsi_device_subtype\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DeviceSubtypeForm.
 */
class DeviceSubtypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $dsi_device_subtype = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dsi_device_subtype->label(),
      '#description' => $this->t("Label for the Device subtype."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dsi_device_subtype->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dsi_device_subtype\Entity\DeviceSubtype::load',
      ],
      '#disabled' => !$dsi_device_subtype->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dsi_device_subtype = $this->entity;
    $status = $dsi_device_subtype->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Device subtype.', [
          '%label' => $dsi_device_subtype->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Device subtype.', [
          '%label' => $dsi_device_subtype->label(),
        ]));
    }
    $form_state->setRedirectUrl($dsi_device_subtype->toUrl('collection'));
  }

}
