<?php

namespace Drupal\dsi_classification\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ClassificationTypeForm.
 */
class ClassificationTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $dsi_classification_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $dsi_classification_type->label(),
      '#description' => $this->t("Label for the Classification type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $dsi_classification_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\dsi_classification\Entity\ClassificationType::load',
      ],
      '#disabled' => !$dsi_classification_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $dsi_classification_type = $this->entity;
    $status = $dsi_classification_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Classification type.', [
          '%label' => $dsi_classification_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Classification type.', [
          '%label' => $dsi_classification_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($dsi_classification_type->toUrl('collection'));
  }

}
