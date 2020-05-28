<?php

namespace Drupal\report\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Form\FormStateInterface;

class ReportColumnsForm extends ReportFiltersForm {
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $report = NULL, $setting_name = NULL) {
    $form = parent::buildForm($form, $form_state, $report, $setting_name);
  
    // Make columns and rows label editable.
    $form['#attached']['library'][] = 'report/report_filters_form';
  
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildFilters($filters) {
    $build = [
      '#type' => 'table',
      '#header' => [$this->t('Item'), ''],
      '#attributes' => [
        'data-theme' => 'report_filters_form',
        'id' => 'predefined_filters_item',
      ],
    ];
  
    foreach ($filters as $id => $filter) {
      $build[$id]['admin_label'] = [
        '#type' => 'textfield',
        '#default_value' => $filter['admin_label'],
        '#size' => '',
      ];
      $build[$id]['delete'] = [
        '#markup' => '<i class="fa fa-remove" />',
      ];
      $build[$id]['filter'] = [
        '#type' => 'hidden',
        '#value' => Json::encode($filter),
      ];
    }
  
    return $build;
  }
  
}
