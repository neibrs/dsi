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
      $form['rows'] = $this->buildFiltersDisplayForm($this->configuration['rows'], Url::fromRoute('entity.report.rows_form', [
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
        $this->findMaxDepth($filter, $top_rows_count);
    }
    
    // Calculate left cols.
    $left_cols_count = 1;
    foreach ($this->configuration['rows'] as $filter) {
        $this->findMaxDepth($filter, $left_cols_count);
    }
    
    // Top left cell.
    $top_left_cell = [
      'data' => '项目',
      'header' => TRUE,
      'colspan' => $left_cols_count,
      'rowspan' => $top_rows_count,
      'class' => ['text-center'],
      'style' => ['vertical-align: middle!important;',]
    ];
    
    // Build top rows.
    $top_rows = [];
    $top_rows[1][] = $top_left_cell;
    foreach ($this->configuration['columns'] as $filter) {
      $this->getTopRows($filter, $top_rows, $top_rows_count);
    }
    
    // Build left cols.
    $left_cols = [];
    $c = 0;
    foreach ($this->configuration['rows'] as $filter) {
      $this->getLeftCols($filter, $left_cols, $left_cols_count, $c);
    }
    $rows = $left_cols;
    
    // Build table content.
    $conditions = $entity->getFiltersOverride();
    $r = 0;
    foreach ($this->configuration['rows'] as $row_config) {
      $this->getTableContent($row_config, $conditions, $rows, $r);
    }
    
    $rows = array_merge($top_rows, $rows);
    $build = [
      '#type' => 'table',
      '#rows' => $rows,
      '#attributes' => [
        'id' => 'report-' . $entity->id(),
        'class' => [
          'table-bordered',
          'table-hover',
          'dataTable',
          'no-box',         // @see table.html.twig
          'table-to-excel', // @see table_to_excel.js
        ],
      ],
      '#attached' => [
        'library' => ['eabax_core/table_to_excel'],
      ]
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
      $this->getBar($column, $filters, $groups, $columns);
    }

    // X axis categories.
    $x_categories = [];
    foreach ($this->configuration['rows'] as $row) {
      $this->getFilterLabels($row, $x_categories);
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
  
  public function getBar($column, $filters, &$groups, &$columns, $label = NULL) {
    $label = $label . $column['filter']['admin_label'];
    
    $column_filters = array_merge($filters, [$column['filter']]);
    if (isset($column['subtree'])) {
      $label = $label . ':';
      
      foreach ($column['subtree'] as $sub_column) {
        $this->getBar($sub_column, $column_filters, $groups,$columns, $label);
      }
    }
    else {
      $c = [];
      $c[] = $label;
      $groups[] = $label;
      
      foreach ($this->configuration['rows'] as $row) {
        $this->getCell($row, $column_filters,  $c, $column['field']);
      }
      $columns[] = $c;
    }
  }
  
  /**
   * Get the maximum depth of the current filter.
   */
  public function findMaxDepth($filter, &$depth) {
    if (isset($filter['subtree'])) {
      foreach ($filter['subtree'] as $sub_filter) {
        if ($depth < $sub_filter['depth']) {
          $depth = $sub_filter['depth'];
        }
        $this->findMaxDepth($sub_filter, $depth);
      }
    }
    else {
      if ($depth < $filter['depth']) {
        $depth = $filter['depth'];
      }
    }
  }
  
  /**
   * Get the number of merged items under current filter.
   */
  public function getMergedItems($filter, $max_sub) {
    if (isset($filter['subtree'])) {
      $max_sub += count($filter['subtree']) - 1;
      
      foreach ($filter['subtree'] as $sub_filter) {
          $max_sub = $this->getMergedItems($sub_filter, $max_sub);
      }
    }
    
    return $max_sub;
  }
  
  public function getTopRows($filter, &$rows, $top_rows_count) {
    $config_header = $filter['filter']['admin_label'];
  
    $rowspan = 1;
    $colspan = 1;
    if (isset($filter['subtree'])) {
      $colspan = $this->getMergedItems($filter, $colspan);
    }
    else
    {
      $max_depth = 1;
      $this->findMaxDepth($filter, $max_depth);
      if ($max_depth < $top_rows_count) {
        $rowspan = $top_rows_count - $max_depth + 1;
      }
    }
    
    $cell = ['data' => $config_header, 'header' => TRUE];
    if ($rowspan > 1) {
      $cell['rowspan'] = $rowspan;
      $cell['style'] = ['vertical-align: middle!important;'];
    }
    if ($colspan > 1) {
      $cell['colspan'] = $colspan;
    }
  
    $cell['class'][] = 'text-center';
    $rows[$filter['depth']][] = $cell;
    
    if (isset($filter['subtree'])) {
      foreach ($filter['subtree'] as $sub_filter) {
        $this->getTopRows($sub_filter, $rows, $top_rows_count);
      }
    }
  }
  
  public function getLeftCols($filter, &$left_cols, $left_cols_count, &$c) {
    $config_header = $filter['filter']['admin_label'];
    
    $rowspan = 1;
    $colspan = 1;
    if (isset($filter['subtree'])) {
      $rowspan = $this->getMergedItems($filter, $rowspan);
    }
    else
    {
      $max_depth = 1;
      $this->findMaxDepth($filter, $max_depth);
      if ($max_depth < $left_cols_count) {
        $colspan = $left_cols_count - $max_depth + 1;
      }
    }
    
    $cell = ['data' => $config_header, 'header' => TRUE];
    if ($rowspan > 1) {
      $cell['rowspan'] = $rowspan;
      $cell['style'] = ['vertical-align: middle!important;'];
    }
    if ($colspan > 1) {
      $cell['colspan'] = $colspan;
    }
  
    $cell['class'][] = 'text-center';
    $left_cols[$c][$filter['depth'] - 1 ] = $cell;
  
    if (isset($filter['subtree'])) {
      foreach ($filter['subtree'] as $sub_filter) {
        $this->getLeftCols($sub_filter, $left_cols, $left_cols_count,$c);
      }
    }
    else {
      $c ++;
    }
  }
  
  public function getTableContent($row_filter, $row_conditions, &$rows, &$r) {
    $row = [];
    
    $row_conditions = array_merge($row_conditions, [$row_filter['filter']]);
    if (isset($row_filter['subtree'])) {
      foreach ($row_filter['subtree'] as $sub_filter) {
        $this->getTableContent($sub_filter, $row_conditions, $rows,$r);
  
      }
    }
    else {
      foreach ($this->configuration['columns'] as $col_config) {
        $this->getCell($col_config, $row_conditions, $row);
      }
  
      $rows[$r] = array_merge($rows[$r], $row);
      $r ++;
    }
  }
  
  public function getCell($col_filter, $conditions, &$row, $field = NULL) {
    $cell_conditions = array_merge($conditions, [$col_filter['filter']]);
    if (isset($col_filter['subtree'])) {
      foreach ($col_filter['subtree'] as $sub_filter) {
        $this->getCell($sub_filter, $cell_conditions, $row, $field);
      }
    }
    else {
      // 除柱状图(bar、stacked_bar)外，先查找行，再查找列，可直接通过列获取 field.
      // 柱状图(bar、stacked_bar)先查找行，再查找列，需要将列的 field 传递.
      if (empty($field) && isset($col_filter['field'])) {
        $field = $col_filter['field'];
      }
      $row[] = $this->gatherData($cell_conditions, $field);
    }
  }
  
  protected function getBaseTables() {
    $base_tables = parent::getBaseTables();
    
    foreach ($this->configuration['rows'] as $column) {
      $this->getSubtreeTables($base_tables, $column);
    }
    
    return $base_tables;
  }
  
}
