<?php

namespace Drupal\organization\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class OrganizationTypeForm.
 */
class OrganizationTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    $organization_type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $organization_type->label(),
      '#description' => $this->t("Label for the Organization type."),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $organization_type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\organization\Entity\OrganizationType::load',
      ],
      '#disabled' => !$organization_type->isNew(),
    ];

    /* You will need additional form elements for your custom properties. */

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $organization_type = $this->entity;
    $status = $organization_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Organization type.', [
          '%label' => $organization_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Organization type.', [
          '%label' => $organization_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($organization_type->toUrl('collection'));
  }

}
