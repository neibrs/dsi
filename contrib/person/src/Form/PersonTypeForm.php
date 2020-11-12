<?php

namespace Drupal\person\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PersonTypeForm.
 */
class PersonTypeForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    $form = parent::form($form, $form_state);

    /** @var \Drupal\person\Entity\PersonTypeInterface $type */
    $type = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $type->label(),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $type->id(),
      '#machine_name' => [
        'exists' => '\Drupal\person\Entity\PersonType::load',
      ],
      '#disabled' => $type->getIsSystem(),
    ];

    $form['alias'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Alias'),
      '#maxlength' => 255,
      '#default_value' => $type->getAlias(),
    ];

    $system_types = [
      'employee' => $this->t('Employee'),
      'applicant' => $this->t('Applicant'),
      'contingent_worker' => $this->t('Contingent worker'),
      'ex_employee' => $this->t('Ex-employee'),
      'ex_applicant' => $this->t('Ex-applicant'),
      'ex_contingent_worker' => $this->t('Ex-contingent worker'),
      'external' => $this->t('External')
    ];
    $form['system_type'] = [
      '#type' => 'select',
      '#title' => $this->t('System type'),
      '#options' => $system_types,
      '#default_value' => $type->getSystemType(),
      '#disabled' => $type->getIsSystem(),
    ];

    $form['status'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Enable'),
      '#default_value' => $type->status(),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $person_type = $this->entity;
    $status = $person_type->save();

    switch ($status) {
      case SAVED_NEW:
        drupal_set_message($this->t('Created the %label Person type.', [
          '%label' => $person_type->label(),
        ]));
        break;

      default:
        drupal_set_message($this->t('Saved the %label Person type.', [
          '%label' => $person_type->label(),
        ]));
    }
    $form_state->setRedirectUrl($person_type->toUrl('collection'));
  }

}
