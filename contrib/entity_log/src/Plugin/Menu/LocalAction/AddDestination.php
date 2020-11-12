<?php

namespace Drupal\entity_log\Plugin\Menu\LocalAction;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\eabax_core\Plugin\Menu\LocalAction\AddDestination as AddDestinationBase;

class AddDestination extends AddDestinationBase {

  /**
   * {@inheritdoc}
   */
  public function getRouteParameters(RouteMatchInterface $route_match) {
    $route_parameters = parent::getRouteParameters($route_match);

    $route_parameters['entity_id'] = $route_match->getRawParameter($route_parameters['entity_type_id']);

    return $route_parameters;
  }
}
