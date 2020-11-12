<?php

namespace Drupal\materialize\Plugin\Preprocess;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("brand_sidebar_block")
 */
class BrandSidebarBlock extends PreprocessBase {

  public function preprocess(array &$variables, $hook, array $info) {
    $x = 'a';
  }

}
