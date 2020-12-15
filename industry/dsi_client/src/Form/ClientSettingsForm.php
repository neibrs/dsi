<?php

namespace Drupal\dsi_client\Form;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class ClientSettingsForm.
 *
 * @ingroup dsi_client
 */
class ClientSettingsForm extends FormBase {

  /**
   * Returns a unique string identifying the form.
   *
   * @return string
   *   The unique string identifying the form.
   */
  public function getFormId() {
    return 'client_settings';
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $config = \Drupal::configFactory()->getEditable('dsi_client.settings');
    $config->set('polling.'. $form_state->getValue('business_group'). '.business_group', $form_state->getValue('business_group'));
    $config->set('polling.'. $form_state->getValue('business_group'). '.person', $form_state->getValue('person'));
    $config->save();

    $this->messenger()->addMessage('保存成功');
    return $form;
  }

  /**
   * Defines the settings form for Client entities.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   *
   * @return array
   *   Form definition array.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $business_groups = \Drupal::entityTypeManager()->getStorage('organization')->loadByProperties([
      'classifications' => 'business_group',
    ]);
    $options = array_map(function ($business_group) {
      return $business_group->label();
    }, $business_groups);
    $form['polling'] = [
      '#type' => 'details',
      '#title' => '轮询规则',
    ];
    $form['polling']['business_group'] = [
      '#type' => 'select',
      '#title' => '律所',
      '#options' => $options,
      '#ajax' => [
        'event' => 'change',
        'callback' => '::businessGroupSwitch',
        'wrapper' => 'person-wrapper',
      ],
    ];

    $form['polling']['person'] = [
      '#title' => $this->t('Persons'),
      '#type' => 'select',
      '#multiple' => TRUE,
      '#prefix' => '<div id="person-wrapper">',
      '#suffix' => '</div>',
    ];
    if ($business_group = $form_state->getValue('business_group')) {
      $organizations = \Drupal::entityTypeManager()->getStorage('organization')->loadAllChildren($business_group, [], TRUE);
      $organizations = array_map(function($organization) {
        return $organization->id();
      }, $organizations);
      $query = \Drupal::entityTypeManager()->getStorage('person')->getQuery();
      $ids = $query->condition('organization', $organizations, 'IN')
        ->execute();
      $persons = \Drupal::entityTypeManager()->getStorage('person')->loadMultiple($ids);
      $persons = array_map(function ($person) {
        return $person->label();
      }, $persons);
      $form['polling']['person']['#options'] = $persons;
    }

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
      '#button_type' => 'primary',
    ];
    return $form;
  }
  /**
   * AJAX callback.
   */
  public function businessGroupSwitch(array $form, FormStateInterface $form_state) {
    return $form['polling']['person'];
  }
}
