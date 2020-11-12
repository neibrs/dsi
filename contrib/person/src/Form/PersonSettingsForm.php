<?php

namespace Drupal\person\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class PersonSettingsForm.
 *
 * @ingroup person
 */
class PersonSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'person_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'person.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('person.settings');

    $token_tree = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['person'],
    ];
    $rendered_token_tree = \Drupal::service('renderer')->render($token_tree);
    $form['encoding_rules'] = [
      '#type' => 'textfield',
      '#title' => t('Encoding rule'),
      '#description' => $this->t('@browse_tokens_link', ['@browse_tokens_link' => $rendered_token_tree]),
      '#default_value' => $config->get('encoding_rules'),
    ];

    $form['initial_password'] = [
      '#title' => $this->t('Initial password'),
      '#type' => 'textfield',
      '#default_value' => $config->get('initial_password'),
      '#required' => TRUE,
    ];

    $form['resume_parsing_api_key'] = [
      '#title' => $this->t('Resume parsing API key'),
      '#type' => 'textfield',
      '#default_value' => $config->get('resume_parsing_api_key'),
    ];
  
    $form['days_before_probation_expires_to_alert'] = [
      '#type' => 'number',
      '#title' => $this->t('Days before probation expires to alter'),
      '#default_value' => $config->get('days_before_probation_expires_to_alert') ?: 0,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('person.settings')
      ->set('encoding_rules', $form_state->getValue('encoding_rules'))
      ->set('initial_password', $form_state->getValue('initial_password'))
      ->set('resume_parsing_api_key', $form_state->getValue('resume_parsing_api_key'))
      ->set('days_before_probation_expires_to_alert', $form_state->getValue('days_before_probation_expires_to_alert'))
      ->save();
  }

}
