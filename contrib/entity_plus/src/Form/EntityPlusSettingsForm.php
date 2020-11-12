<?php

namespace Drupal\entity_plus\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class EntityPlusSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'entity_plus.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'entity_plus_settings_form';
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('entity_plus.settings');

    $form['entity_view_display_empty_field'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Display empty field'),
      '#default_value' => $config->get('entity_view_display_empty_field'),
    ];

    return $form;
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('entity_plus.settings')
      ->set('entity_view_display_empty_field', $form_state->getValue('entity_view_display_empty_field'))
      ->save();
  }

}
