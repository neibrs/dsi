<?php

namespace Drupal\views_plus\Controller;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\views\Routing\ViewPageController as ViewPageControllerBase;

/**
 * Extends for provide views_plus_view alter.
 */
class ViewPageController extends ViewPageControllerBase {

  /**
   * {@inheritdoc}
   */
  public function handle($view_id, $display_id, RouteMatchInterface $route_match) {
    $build = parent::handle($view_id, $display_id, $route_match);

    \Drupal::moduleHandler()->alter('views_plus_view', $build, $view_id, $display_id);

    return $build;
  }

}
