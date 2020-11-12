<?php

namespace Drupal\materialize\Plugin\Preprocess;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("views_view_table")
 */
class ViewsViewTable extends PreprocessBase {

  /**
   * {@inheritDoc}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    // 行高亮
    $variables['attributes']['class'][] = 'highlight';
    $variables['#attached']['library'][] = 'materialize/datatables';
  }
}
