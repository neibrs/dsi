<?php

namespace Drupal\dsi_device_other_subtype\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class OtherSubtypeForm.
 */
class OtherSubtypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $dsi_device_other_subtype = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dsi_device_other_subtype->label(),
      '#description' => $this->t("Label for the Other subtype."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dsi_device_other_subtype->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dsi_device_other_subtype\Entity\OtherSubtype::load',
      ],
      '#disabled' => !$dsi_device_other_subtype->isNew(),
    ];

    $locations = $this->entityTypeManager->getStorage('dsi_device_oslc')->loadMultiple();
    $locations = array_map(function ($location) {
      return $location->label();
    }, $locations);

    $form['locations'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Display the following location choices'),
      '#options' => $locations,
      '#default_value' => $this->entity->getLocations(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dsi_device_other_subtype = $this->entity;
    $status = $dsi_device_other_subtype->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Other subtype.', [
          '%label' => $dsi_device_other_subtype->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Other subtype.', [
          '%label' => $dsi_device_other_subtype->label(),
        ]));
    }
    $form_state->setRedirectUrl($dsi_device_other_subtype->toUrl('collection'));
  }

}
