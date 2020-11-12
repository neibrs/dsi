<?php

namespace Drupal\dsi_device_other_subtype\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class LocationChoicesForm.
 */
class LocationChoicesForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $dsi_device_oslc = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dsi_device_oslc->label(),
      '#description' => $this->t("Label for the Location choices."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dsi_device_oslc->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dsi_device_other_subtype\Entity\LocationChoices::load',
      ],
      '#disabled' => !$dsi_device_oslc->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dsi_device_oslc = $this->entity;
    $status = $dsi_device_oslc->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Location choices.', [
          '%label' => $dsi_device_oslc->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Location choices.', [
          '%label' => $dsi_device_oslc->label(),
        ]));
    }
    $form_state->setRedirectUrl($dsi_device_oslc->toUrl('collection'));
  }

}
