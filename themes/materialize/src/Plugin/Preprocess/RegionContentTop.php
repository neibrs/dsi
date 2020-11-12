<?php

namespace Drupal\materialize\Plugin\Preprocess;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("region__content_top")
 */
class RegionContentTop extends PreprocessBase {

  public function preprocess(array &$variables, $hook, array $info) {
    $variables['attributes'] = [
      'class' => [
        'breadcrumbs-dark pb-0',
      ],
      'id' => 'breadcrumbs-wrapper',
    ];
  }

}
