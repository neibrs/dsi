<?php

namespace Drupal\views_plus\Plugin\Menu\LocalAction;

use Drupal\Core\Menu\LocalActionDefault;
use Drupal\Core\Routing\RouteMatchInterface;

class ConfigExport extends LocalActionDefault {

  /**
   * {@inheritdoc}
   */
  public function getRouteParameters(RouteMatchInterface $route_match) {
    $route_parameters = parent::getRouteParameters($route_match);

    $route_parameters['config_type'] = 'view';
    if ($view = $route_match->getParameter('view')) {
      $route_parameters['config_name'] = $view->id();
    }

    return $route_parameters;
  }

}
