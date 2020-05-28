<?php

namespace Drupal\report\Plugin\Report;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\report\Entity\ReportInterface;
use Drupal\report\Plugin\ReportPluginBase;

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
      $form['columns'] = \Drupal::service('entity_filter.manager')
        ->buildFiltersDisplayForm($this->configuration['columns'], Url::fromRoute('entity.report.columns_form', [
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
      $column_filters = array_merge($filters, [$column]);
      $columns[] = $this->gatherData($column_filters);
      $categories[] = $column['admin_label'];
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

  /**
   * Build bar chart.
   *
   * @return array
   */
  public function donut(ReportInterface $entity) {
    $filters = $entity->getFiltersOverride();
    foreach ($this->configuration['columns'] as $column) {
      $column_filters = array_merge($filters, [$column]);
      $columns[] = [
        $column['admin_label'],
        $this->gatherData($column_filters),
      ];
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

}
