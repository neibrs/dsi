<?php

namespace Drupal\organization\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class OrganizationClassificationForm.
 */
class OrganizationClassificationForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $organization_classification = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $organization_classification->label(),
      '#description' => $this->t("Label for the Organization classification."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $organization_classification->id(),
      '#machine_name' => [
        'exists' => '\Drupal\organization\Entity\OrganizationClassification::load',
      ],
      '#disabled' => !$organization_classification->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $organization_classification = $this->entity;
    $status = $organization_classification->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Organization classification.', [
          '%label' => $organization_classification->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Organization classification.', [
          '%label' => $organization_classification->label(),
        ]));
    }
    $form_state->setRedirectUrl($organization_classification->toUrl('collection'));
  }

}
