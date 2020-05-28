<?php

namespace Drupal\views_table_multi_header;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\style\Table;

/**
 * Override views table style.
 */
class MultiHeaderTable extends Table {

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);

    $columns = $this->sanitizeColumns($this->options['columns']);
    foreach ($columns as $field => $column) {
      $form['info'][$field]['group'] = [
        '#title' => $this->t('Group'),
        '#title_display' => 'invisible',
        '#type' => 'textfield',
        '#size' => 10,
        // '#description' => $this->t('Separate multiple groups by commas.'),
        '#default_value' => !empty($this->options['info'][$field]['group']) ? $this->options['info'][$field]['group'] : '',
      ];
    }

    $form['multi_header']['info'] = $form['info'];
    $form['multi_header']['columns'] = $form['columns'];
  }

}
