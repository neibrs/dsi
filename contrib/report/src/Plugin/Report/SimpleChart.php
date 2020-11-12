<?php

namespace Drupal\report\Plugin\Report;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\report\Entity\ReportInterface;
use Drupal\report\Plugin\ReportPluginBase;
use Drupal\views\Views;

/**
 * @Report(
 *   id = "simple_chart",
 *   label = @Translation("Simple chart"),
 * )
 */
class SimpleChart extends ReportPluginBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'columns' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $report = $form_state->getBuildInfo()['callback_object']->getEntity();
    if (!$report->isNew()) {
      $form['columns'] = $this->buildFiltersDisplayForm($this->configuration['columns'], Url::fromRoute('entity.report.columns_form', [
          'report' => $report->id(),
        ], [
          'query' => \Drupal::destination()->getAsArray(),
        ]));
      $form['columns']['label']['#title'] = t('Columns');
    }

    return $form;
  }

  protected function getStyles() {
    return [
      'bar' => [
        'label' => $this->t('Bar chart'),
        'icon' => 'fa-bar-chart',
      ],
      'donut' => [
        'label' => $this->t('Donut chart'),
        'icon' => 'fa-circle-o',
      ],
    ];
  }

  /**
   * Build bar chart.
   *
   * @return array
   */
  public function bar(ReportInterface $entity) {
    // Build the chart.
    $columns = [$entity->label()];
    $categories = [];

    $filters = $entity->getFiltersOverride();
    foreach ($this->configuration['columns'] as $column) {
      $this->getBar($column, $filters, $columns,$categories);
    }

    $chart_config = [
      'data' => [
        'columns' => [
          $columns,
        ],
        'types' => [
          $entity->label() => 'bar',
        ],
      ],
      'axis' => [
        'x' => [
          'type' => 'category',
          'categories' => $categories,
        ],
      ],
    ];

    return $this->buildChart($entity, $chart_config);
  }
  
  public function getBar($column, $filters, &$columns, &$categories, $label = NULL) {
    $label = $label . $column['filter']['admin_label'];
  
    $column_filters = array_merge($filters, [$column['filter']]);
    if (isset($column['subtree'])) {
      $label = $label . ':';
    
      foreach ($column['subtree'] as $sub_column) {
        $this->getBar($sub_column, $column_filters, $columns,$categories, $label);
      }
    }
    else {
      $categories[] = $label;
      
      $field = [];
      if (isset($column['field'])) {
        $field = $column['field'];
      }
      $columns[] = $this->gatherData($column_filters, $field);
    }
  }

  /**
   * Build bar chart.
   *
   * @return array
   */
  public function donut(ReportInterface $entity) {
    $filters = $entity->getFiltersOverride();
    
    $columns = [];
    foreach ($this->configuration['columns'] as $column) {
      $this->getDonut($column, $filters,  $columns);
    }

    $chart_config = [
      'data' => [
        'columns' => $columns,
        'type' => 'donut',
      ],
      'donut' => [
        'title' => $entity->label(),
      ],
    ];

    return $this->buildChart($entity, $chart_config);
  }
  
  public function getDonut($column, $conditions, &$columns, $label = NULL) {
    $label = $label . $column['filter']['admin_label'];
    
    $cell_conditions = array_merge($conditions, [$column['filter']]);
    if (isset($column['subtree'])) {
      $label = $label . ':';
      foreach ($column['subtree'] as $sub_column) {
        $this->getDonut($sub_column, $cell_conditions, $columns, $label);
      }
    }
    else {
      $columns[] = [
        $label,
        $this->gatherData($cell_conditions, $column['field']),
      ];
    }
  }

  /**
   * Build chart.
   *
   * ReportController::ajaxStyle use #report-ID as selector, so ".c3-chart" must within "#report-ID".
   *
   * @see \Drupal\report\Controller\ReportController::ajaxStyle()
   */
  protected function buildChart(ReportInterface $entity, $chart_config) {
    // Because of ReportController::ajaxStyle, every style need a difference data-id.
    $data_id = $this->configuration['style'] . '-' . $entity->id();

    $build = [
      '#markup' => '<div class="c3-chart" data-id="' . $data_id .'"></div>',
      '#prefix' => '<div id="report-' . $entity->id() . '">',
      '#suffix' => '</div>',
    ];

    $build['#attached']['drupalSettings']['c3Charts'][$data_id] = $chart_config;
    $build['#attached']['library'][] = 'eabax_core/c3_chart';

    // Set cache tags.
    $build['#cache']['tags'] = Cache::mergeTags($this->getCacheTags(), $entity->getCacheTags());

    return $build;
  }
  
  protected function getBaseTables() {
    $base_tables = parent::getBaseTables();
  
    foreach ($this->configuration['columns'] as $column) {
      $this->getSubtreeTables($base_tables, $column);
    }
    
    return $base_tables;
  }
  
  protected function getSubtreeTables(&$base_tables, $filter) {
    $base_table = $filter['filter']['table'];
    if (!isset($base_tables[$base_table])) {
      $base_tables[$base_table] = $base_table;
    }
    
    if (isset($filter['subtree'])) {
      foreach ($filter['subtree'] as $sub_filter) {
        $this->getSubtreeTables($base_tables, $sub_filter);
      }
    }
  }
  
}
