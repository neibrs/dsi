<?php

namespace Drupal\organization\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class OrganizationSettingsForm.
 *
 * @ingroup organization
 */
class OrganizationSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['organization.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'organization.settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);
    $config = $this->config('organization.settings');

    $business_groups = \Drupal::entityTypeManager()->getStorage('organization')->loadByProperties(['classifications' => ['business_group']]);

    $business_groups = array_map(function ($business_group){
      return $business_group->label();
    }, $business_groups);

    $form['default_business_group'] = [
      '#type' => 'select',
      '#options' => $business_groups,
      '#title' => $this->t('Default business group'),
      '#default_value' => $config->get('default_business_group'),
    ];

    $token_tree = [
      '#theme' => 'token_tree_link',
      '#token_types' => ['organization'],
    ];
    $rendered_token_tree = \Drupal::service('renderer')->render($token_tree);
    $form['encoding_rules'] = [
      '#type' => 'textfield',
      '#title' => t('Encoding rule'),
      '#description' => $this->t('@browse_tokens_link', ['@browse_tokens_link' => $rendered_token_tree]),
      '#default_value' => $config->get('encoding_rules'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    parent::submitForm($form, $form_state);

    $this->config('organization.settings')
      ->set('encoding_rules', $form_state->getValue('encoding_rules'))
      ->save();
    /** @see organization_entity_base_field_info_alter() */
    \Drupal::entityTypeManager()->clearCachedDefinitions();
  }

}
