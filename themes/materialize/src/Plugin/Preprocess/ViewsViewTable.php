<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\Core\Template\Attribute;

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
    parent::preprocess($variables, $hook, $info);
    // 行高亮
    $variables['attributes']['class'][] = 'highlight';
  }

}
