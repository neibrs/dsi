<?php

namespace Drupal\views_plus\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\NumericFilter;

/**
 * @ViewsFilter("month")
 */
class MonthFilter extends NumericFilter {

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    parent::valueForm($form, $form_state);

    foreach (['value', 'min', 'max'] as $delta) {
      if (isset($form['value'][$delta])) {
        $form['value'][$delta]['#attributes']['placeholder'] = $this->t('Format: YYYY-MM. Example: @example', [
          '@example' => date('Y-m'),
        ]);
      }
    }
  }

}
