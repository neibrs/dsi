<?php

namespace Drupal\organization\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\organization\Entity\Organization;
use Drupal\organization\Entity\OrganizationInterface;

class OrganizationMergeForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'organization_merge_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $organization = NULL) {
    if (is_string($organization)) {
      $organization = \Drupal::entityTypeManager()->getStorage('organization')->load($organization);
    }
    $form['from_organization'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('From organization'),
      '#target_type' => 'organization',
      '#default_value' => $organization,
      '#required' => TRUE,
    ];

    $form['to_organization'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('To organization'),
      '#target_type' => 'organization',
      '#required' => TRUE,
    ];
    
    $form['merge_date'] = [
      '#type' => 'date',
      '#title' => $this->t('Merge time'),
      '#required' => TRUE,
    ];
    
    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Merge organizations'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    /** @var \Drupal\organization\OrganizationStorageInterface $organization_storage */
    $organization_storage = \Drupal::service('entity_type.manager')->getStorage('organization');
  
    $from = $organization_storage->load($form_state->getValue('from_organization'));
    $to   = $organization_storage->load($form_state->getValue('to_organization'));
    $merge_date = $form_state->getValue('merge_date');

    $from->effective_dates->end_value = $merge_date;
    $from->save();

    // Invoke hooks
    $context = [
      'merge_date' => $merge_date,
    ];
    \Drupal::moduleHandler()->invokeAll('organization_merge', [$from, $to, $context]);
  
    drupal_set_message($this->t('Merge organization has success.'));
  }
}
