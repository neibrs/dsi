<?php

namespace Drupal\views_plus\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;

trait DateTrait {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    // value is already set up properly, we're just adding our new field to it.
    $options['value']['contains']['range']['default'] = 'this_month';

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    parent::valueForm($form, $form_state);

    if (isset($form['value']['value'])) {
      $form['value']['value']['#type'] = 'date';
    }

    if (isset($form['value']['min'])) {
      $form['value']['min']['#type'] = 'date';
    }

    if (isset($form['value']['max'])) {
      $form['value']['max']['#type'] = 'date';
    }

    $form['value']['type']['#options']['range'] = $this->t('Select date range.');

    $form['value']['range'] = [
      '#type' => 'select',
      '#title' => $this->t('Date range'),
      '#options' => [
        'this_week' => $this->t('This week'),
        'previous_week' => $this->t('Previous week'),
        'this_month' => $this->t('This month'),
        'previous_month' => $this->t('Previous month'),
        // 'this_quarter' => $this->t('This quarter'),
        // 'previous_quarter' => $this->t('Previous quarter'),
        'this_year' => $this->t('This year'),
        'previous_year' => $this->t('Previous year'),
      ],
      '#states' => [
        'visible' => [
          'input[name="options[value][type]"]' => ['value' => 'range'],
        ],
      ],
    ];

    // Hide date range.
    $min_invisible = $form['value']['value']['#states']['visible'];
    $value_invisible = $form['value']['min']['#states']['visible'];
    unset($form['value']['value']['#states']['visible']);
    unset($form['value']['min']['#states']['visible']);
    unset($form['value']['max']['#states']['visible']);

    $min_invisible[] = [':input[name="options[value][type]"]' => ['value' => 'range']];
    $value_invisible[] = [':input[name="options[value][type]"]' => ['value' => 'range']];

    $form['value']['value']['#states']['invisible'] = $value_invisible;
    $form['value']['min']['#states']['invisible'] = $min_invisible;
    $form['value']['max']['#states']['invisible'] = $min_invisible;
  }

  /**
   * {@inheritdoc}
   */
  public function validateOptionsForm(&$form, FormStateInterface $form_state) {
    $value = $form_state->getValue(['options', 'value']);
    if ($value['type'] == 'range') {
      // 临时解决校验报错
      $value['value'] = '2020-01-02';
      $value['min'] = '2020-01-03';
      $value['max'] = '2020-01-04';
    }
    $form_state->setValue(['options', 'value'], $value);

    parent::validateOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  protected function opSimple($field) {
    if ($this->value['type'] == 'range') {
      switch ($this->value['range']) {
        case 'this_week':
          $this->value['value'] = date('Y-m-d', strtotime('first day of this week'));
          break;

        case 'previous_week':
          $this->value['value'] = date('Y-m-d', strtotime('first day of previous week'));
          break;

        case 'this_month':
          $this->value['value'] = date('Y-m-d', strtotime('first day of this month'));
          break;

        case 'previous_month':
          $this->value['value'] = date('Y-m-d', strtotime('first day of previous month'));
          break;

        /*case 'this_quarter':
        break;
        case 'previous_quarter':
        break;*/
        case 'this_year':
          $this->value['value'] = date('Y-m-d', strtotime('1/1 this year'));
          break;

        case 'previous_year':
          $this->value['value'] = date('Y-m-d', strtotime('1/1 previous year'));
          break;

        default:
          throw new \InvalidArgumentException(sprintf('Date range %s is invalid.', $this->value['type']));
      }
    }

    parent::opSimple($field);
  }

  /**
   * {@inheritdoc}
   */
  protected function opBetween($field) {
    if ($this->value['type'] == 'range') {
      switch ($this->value['range']) {
        case 'this_week':
          $this->value['min'] = date('Y-m-d', strtotime('first day of this week'));
          $this->value['max'] = date('Y-m-d', strtotime('this Sunday'));
          break;

        case 'previous_week':
          $this->value['min'] = date('Y-m-d', strtotime('first day of previous week'));
          $this->value['max'] = date('Y-m-d', strtotime('last Sunday'));
          break;

        case 'this_month':
          $this->value['min'] = date('Y-m-d', strtotime('first day of this month'));
          $this->value['max'] = date('Y-m-d', strtotime('last day of this month'));
          break;

        case 'previous_month':
          $this->value['min'] = date('Y-m-d', strtotime('first day of previous month'));
          $this->value['max'] = date('Y-m-d', strtotime('last day of previous month'));
          break;

        /*case 'this_quarter':
        break;
        case 'previous_quarter':
        break;*/
        case 'this_year':
          $this->value['min'] = date('Y-m-d', strtotime('1/1 this year'));
          $this->value['max'] = date('Y-m-d', strtotime('1/1 next year -1 day'));
          break;

        case 'previous_year':
          $this->value['min'] = date('Y-m-d', strtotime('1/1 previous year'));
          $this->value['max'] = date('Y-m-d', strtotime('1/1 this year -1 day'));
          break;

        default:
          throw new \InvalidArgumentException(sprintf('Date range %s is invalid.', $this->value['type']));
      }
    }

    parent::opBetween($field);
  }

  /**
   * {@inheritdoc}
   */
  public function adminSummary() {
    if ($this->value['type'] == 'range') {
      if ($this->isAGroup()) {
        return $this->t('grouped');
      }
      if (!empty($this->options['exposed'])) {
        return $this->t('exposed');
      }

      $options = [
        'this_week' => $this->t('This week'),
        'previous_week' => $this->t('Previous week'),
        'this_month' => $this->t('This month'),
        'previous_month' => $this->t('Previous month'),
        // 'this_quarter' => $this->t('This quarter'),
        // 'previous_quarter' => $this->t('Previous quarter'),
        'this_year' => $this->t('This year'),
        'previous_year' => $this->t('Previous year'),
      ];
      $operators = $this->operatorOptions('short');
      if (in_array($this->operator, ['between', 'not between'])) {
        $output = $this->operator == 'between' ? $this->t('in') : $this->t('not in');
        $output .= ' ' . $options[$this->value['range']];
      }
      else {
        $output = $operators[$this->operator] . $options[$this->value['range']];
      }

      return $output;
    }

    return parent::adminSummary();
  }

}
