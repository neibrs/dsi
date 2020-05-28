<?php

namespace Drupal\report\Plugin\Report;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Drupal\report\Entity\ReportInterface;

/**
 * @Report(
 *   id = "cross_table",
 *   label = @Translation("Cross table"),
 * )
 */
class CrossTable extends SimpleChart {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'rows' => [],
      'style' => 'table',
    ] + parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);

    $report = $form_state->getBuildInfo()['callback_object']->getEntity();
    if (!$report->isNew()) {
      $form['rows'] = \Drupal::service('entity_filter.manager')
        ->buildFiltersDisplayForm($this->configuration['rows'], Url::fromRoute('entity.report.rows_form', [
          'report' => $report->id(),
        ], [
          'query' => \Drupal::destination()->getAsArray(),
        ]));
      $form['rows']['label']['#title'] = t('Rows');
    }

    return $form;
  }

  protected function getStyles() {
    return [
      'stacked_bar' => [
        'label' => $this->t('Stacked bar chart'),
        'icon' => 'fa-area-chart',
      ],
      'table' => [
        'label' => $this->t('Table'),
        'icon' => 'fa-table',
      ],
    ] + parent::getStyles();
  }

  /**
   * Style callback for table.
   *
   * @return array
   */
  public function table(ReportInterface $entity) {
    // Calculate top rows count.
    $top_rows_count = 1;
    foreach ($this->configuration['columns'] as $filter) {
      if (!empty($filter['category'])) {
        $header_count = substr_count($filter['category'],'|') + 2;
        if ($header_count > $top_rows_count) {
          $top_rows_count = $header_count;
        }
      }
    }

    // Calculate left cols.
    $left_cols_count = 1;
    foreach ($this->configuration['rows'] as $filter) {
      if (!empty($filter['category'])) {
        $header_count = substr_count($filter['category'],'|') + 2;
        if ($header_count > $left_cols_count) {
          $left_cols_count = $header_count;
        }
      }
    }

    // Top left cell.
    $top_left_cell = [
      'data' => '项目',
      'header' => TRUE,
      'colspan' => $left_cols_count,
      'rowspan' => $top_rows_count
    ];

    // Build top rows.
    $top_rows = [];
    $top_rows[1][] = $top_left_cell;
    foreach ($this->configuration['columns'] as $filter) {
      $rowspan = $top_rows_count;
      $r = 1;
      $config_headers = explode(':', $filter['admin_label']);
      $rowspan = $top_rows_count - count($config_headers);
      foreach ($config_headers as $config_header) {
        // Cell merge.
        if (!empty($top_rows[$r])) {
          $prev = &$top_rows[$r][count($top_rows[$r]) - 1];
          if ($prev['data'] == $config_header) {
            $prev['colspan'] = isset($prev['colspan']) ? $prev['colspan'] + 1 : 2;
            $r ++;
            continue;
          }
        }

        $cell = ['data' => $config_header, 'header' => TRUE];
        if ($rowspan > 1) {
          $cell['rowspan'] = $rowspan;
        }
        $top_rows[$r][] = $cell;

        if ($rowspan > 1) {
          $r += $rowspan;
          $rowspan = 1;
        }
        else {
          $r ++;
        }
      }

    }

    // Build left cols.
    $left_cols = [];
    foreach ($this->configuration['rows'] as $filter) {
      $row = [];

      $colspan = $left_cols_count;
      $c = 1;
      $config_headers = explode(':', $filter['admin_label']);
      $colspan = $left_cols_count- count($config_headers);
      foreach ($config_headers as $config_header) {
        // Cell merge.
        unset($prev);  // Prevent data to be changed to NULL.
        $prev = NULL;
        if (!empty($left_cols)) {
          foreach ($left_cols as $r => $left_col) {
            if (isset($left_col[$c])) {
              $prev = &$left_cols[$r];
            }
          }
        }
        if ($prev && $prev[$c]['data'] == $config_header) {
          $prev[$c]['rowspan'] = isset($prev[$c]['rowspan']) ? $prev[$c]['rowspan'] + 1 : 2;
          $c ++;
          continue;
        }

        $row[$c] = ['data' => $config_header, 'header' => TRUE];
        if ($colspan > 1) {
          $row[$c]['colspan'] = $colspan;
          $c += $colspan;
          $colspan = 1;
        }
        else {
          $c ++;
        }
      }

      $left_cols[] = $row;
    }
    $rows = $left_cols;

    // Build table content.
    $conditions = $entity->getFiltersOverride();
    $r = 0;
    foreach ($this->configuration['rows'] as $row_config) {
      $row = [];
      $row_conditions = array_merge($conditions, [$row_config]);
      foreach ($this->configuration['columns'] as $col_config) {
        $cell_conditions = array_merge($row_conditions, [$col_config]);
        $row[] = $this->gatherData($cell_conditions);
      }

      $rows[$r] = array_merge($rows[$r], $row);
      $r++;
    }

    $rows = array_merge($top_rows, $rows);
    $build = [
      '#type' => 'table',
      '#rows' => $rows,
      '#attributes' => [
        'id' => 'report-' . $entity->id(),
      ],
    ];

    // Set cache tags.
    $build['#cache']['tags'] = Cache::mergeTags($entity->getCacheTags(), $this->getCacheTags());

    return $build;
  }

  /**
   * Style callback for bar chart.
   *
   * @return array
   */
  public function bar(ReportInterface $entity) {
    return $this->stacked_bar($entity, FALSE);
  }

  /**
   * Style callback for stacked bar chart.
   *
   * @return array
   */
  public function stacked_bar(ReportInterface $entity, $stacked = TRUE) {
    // Columns and groups.
    $columns = [];
    $groups = [];
    $filters = $entity->getFiltersOverride();
    foreach ($this->configuration['columns'] as $column) {
      $column_filters = array_merge($filters, [$column]);

      $c = [];
      $c[] = $column['admin_label'];
      $groups[] = $column['admin_label'];
      foreach ($this->configuration['rows'] as $row) {
        $cell_filters = array_merge($column_filters, [$row]);
        $c[] = $this->gatherData($cell_filters);
      }
      $columns[] = $c;
    }

    // X axis categories.
    $x_categories = [];
    foreach ($this->configuration['rows'] as $row) {
      $x_categories[] = $row['admin_label'];
    }

    $chart_config = [
      'data' => [
        'columns' => $columns,
        'type' => 'bar',
      ],
      'axis' => [
        'x' => [
          'type' => 'category',
          'categories' => $x_categories,
        ],
      ],
    ];
    if ($stacked) {
      $chart_config['data']['groups'] = [$groups];
    }

    return $this->buildChart($entity, $chart_config);
  }



}
