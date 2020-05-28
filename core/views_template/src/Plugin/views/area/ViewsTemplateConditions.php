<?php

namespace Drupal\views_template\Plugin\views\area;

use Drupal\views\Plugin\views\area\AreaPluginBase;

/**
 * @ViewsArea("views_template_conditions")
 */
class ViewsTemplateConditions extends AreaPluginBase {

  /**
   * {@inheritdoc}
   */
  public function render($empty = FALSE) {
    $build = [];

    $build['rows'] = [
      '#markup' => $this->t('Total rows: @total_rows &emsp;', [
        '@total_rows' => $this->view->total_rows,
      ]),
    ];

    $override = \Drupal::service('views_template.manager')->getViewOverride($this->view->storage);
    if (!empty($override['filters'])) {
      $filters = array_map(function ($filter) {
        return $filter['admin_label'];
      }, $override['filters']);
      $build['conditions'] = [
        '#markup' => $this->t('Conditions: @conditions', ['@conditions' => implode(', ', $filters)]),
      ];
    }

    return $build;
  }

}
