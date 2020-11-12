<?php

namespace Drupal\dsi_hardware\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class HardwareTypeForm.
 */
class HardwareTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $dsi_hardware_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dsi_hardware_type->label(),
      '#description' => $this->t("Label for the Hardware type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dsi_hardware_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dsi_hardware\Entity\HardwareType::load',
      ],
      '#disabled' => !$dsi_hardware_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dsi_hardware_type = $this->entity;
    $status = $dsi_hardware_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Hardware type.', [
          '%label' => $dsi_hardware_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Hardware type.', [
          '%label' => $dsi_hardware_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($dsi_hardware_type->toUrl('collection'));
  }

}
