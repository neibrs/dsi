<?php

namespace Drupal\alert\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class AlertTypeForm.
 */
class AlertTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $type->label(),
      '#description' => $this->t("Label for the Alert type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\alert\Entity\AlertType::load',
      ],
      '#disabled' => !$type->isNew(),
    ];

    $form['description'] = [
      '#title' => t('Description'),
      '#type' => 'textarea',
      '#default_value' => $type->getDescription(),
    ];

    $form['color'] = [
      '#type' => 'color',
      '#title' => $this->t('Color'),
      '#default_value' => $type->getColor(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $alert_type = $this->entity;
    $status = $alert_type->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addMessage($this->t('Created the %label Alert type.', [
          '%label' => $alert_type->label(),
        ]));
        break;

      default:
        $this->messenger()->addMessage($this->t('Saved the %label Alert type.', [
          '%label' => $alert_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($alert_type->toUrl('collection'));
  }

}
