<?php

namespace Drupal\organization_hierarchy\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $route = $collection->get('organization.chart');
    $route->setDefault('_form', '\Drupal\organization_hierarchy\Form\OrganizationChartForm');
  }
}