<?php

namespace Drupal\data_model\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a 'DataModelBlock' block.
 *
 * @Block(
 *  id = "data_model_block",
 *  admin_label = @Translation("Data model block"),
 * )
 */
class DataModelBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'entity_types' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form['entity_types'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Entity types'),
      '#default_value' => $this->configuration['entity_types'],
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockValidate($form, FormStateInterface $form_state) {
    // TODO: check entity_types
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    $this->configuration['entity_types'] = $form_state->getValue('entity_types');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $entity_types = explode(',', $this->configuration['entity_types']);

    $build = [];
    $build['data_model']['#markup'] = '<div id="data-model"></div>';
    $build['data_model']['#attached']['library'][] = 'data_model/data_model';
    $build['data_model']['#attached']['drupalSettings']['data_model']['entity_types'] = $entity_types;

    return $build;
  }

}
