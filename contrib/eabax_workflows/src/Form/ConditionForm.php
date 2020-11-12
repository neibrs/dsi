<?php

namespace Drupal\eabax_workflows\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ConditionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'workflow_transition_condition';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $workflow = NULL, $transition = NULL) {
    $workflow = \Drupal::entityTypeManager()->getStorage('workflow')->load($workflow);
    $condition_id = $form_state->get('condition_id');

    // TODO Replace transition condition expression.
    $configuration = $this->conditionExpression->getConfiguration();

    if (empty($condition_id) && !empty($configuration['condition_id'])) {
      $condition_id = $configuration['condition_id'];
      $form_state->set('condition_id', $condition_id);
    }

    // Step 1 of the multistep form.
    if (!$condition_id) {
      $condition_definitions = $this->conditionManager->getGroupedDefinitions();
      $options = [];
      foreach ($condition_definitions as $group => $definitions) {
        foreach ($definitions as $id => $definition) {
          $options[$group][$id] = $definition['label'];
        }
      }

      $form['condition_id'] = [
        '#type' => 'select',
        '#title' => $this->t('Condition'),
        '#options' => $options,
        '#required' => TRUE,
      ];
      $form['continue'] = [
        '#type' => 'submit',
        '#value' => $this->t('Continue'),
        '#name' => 'continue',
        // Only validate the selected condition in the first step.
        '#limit_validation_errors' => [['condition_id']],
        '#submit' => [static::class . '::submitFirstStep'],
      ];

      return $form;
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Only if there is a condition selected already we can validate something.
    if ($form_state->get('condition_id')) {
      // Invoke the submission handler which will setup the expression being
      // edited in the form. That way the expression is ready for other
      // validation handlers.
      $this->submitForm($form, $form_state);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }

  /**
   * Submit callback: save the selected condition in the first step.
   */
  public static function submitFirstStep(array &$form, FormStateInterface $form_state) {
    $form_state->set('condition_id', $form_state->getValue('condition_id'));
    $form_state->setRebuild();
  }

}
