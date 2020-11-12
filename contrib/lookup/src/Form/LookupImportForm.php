<?php

namespace Drupal\lookup\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\dsi_import\Form\ImportForm;

class LookupImportForm extends ImportForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL) {
    $form = parent::buildForm($form, $form_state, 'lookup');

    $entities = \Drupal::entityTypeManager()->getStorage('lookup_type')->loadMultiple();
    $options = array_map(function ($entity) {
      return $entity->label();
    }, $entities);
    $form['type'] = [
      '#title' => $this->t('Lookup type'),
      '#type' => 'select',
      '#options' => $options,
      '#required' => TRUE,
      '#weight' => -10,
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $configuration['source']['path'] = $this->file->getFileUri();
    $configuration['source']['entity_type_id'] = $this->entity_type_id;

    $configuration['source']['type'] = $form_state->getValue('type');

    $options = [
      'limit' => 0,
      'update' => $form_state->getValue('update'),
      'force' => 0,
      'configuration' => $configuration,
    ];

    $this->doMigrate($options, $form_state);
  }

}
