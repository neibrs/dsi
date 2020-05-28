<?php

namespace Drupal\eabax_core\Plugin\BlockStyle;

use Drupal\block_style_plugins\Plugin\BlockStyleBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * @BlockStyle(
 *  id = "box",
 *  label = @Translation("Box"),
 * )
 */
class Box extends BlockStyleBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'box_class' => '',
      'box_style' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form['box_style'] = [
      '#type' => 'select',
      '#title' => 'Box style',
      '#options' => [
        '' => $this->t('- None -'),
        'box-default' => 'Default',
        'box-primary' => 'Primary',
        'box-success' => 'Success',
        'box-info' => 'Info',
        'box-danger' => 'Danger',
        'box-warning' => 'Warning',
      ],
      '#default_value' => $this->configuration['box_style'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (empty($this->configuration['box_style'])) {
      $this->configuration['box_class'] = '';
    }
    else {
      $this->configuration['box_class'] = 'box';
    }
  }

}
