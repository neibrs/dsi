<?php

namespace Drupal\entity_plus\EventSubscriber;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $route = $collection->get('system.entity_autocomplete');
    $route->setDefault('_controller', '\Drupal\entity_plus\Controller\EntityAutocompleteController::handleAutocomplete');
  }

}
