<?php

namespace Drupal\entity_plus\Enhancer;

use Drupal\Core\Routing\EnhancerInterface;
use Drupal\entity_plus\Controller\EntityPlusController;
use Symfony\Cmf\Component\Routing\RouteObjectInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class EntityRouteEnhancer implements EnhancerInterface {

  /**
   * {@inheritdoc}
   */
  public function enhance(array $defaults, Request $request) {
    $route = $defaults[RouteObjectInterface::ROUTE_OBJECT];
    if (!$this->applies($route)) {
      return $defaults;
    }

    if (empty($defaults['_controller'])) {
      if (!empty($defaults['_perform_operation'])) {
        $defaults = $this->enhancePerformOperation($defaults, $request);
      }
    }
    return $defaults;
  }

  /**
   * Returns whether the enhancer runs on the current route.
   *
   * @param \Symfony\Component\Routing\Route $route
   *   The current route.
   *
   * @return bool
   */
  protected function applies(Route $route) {
    return !$route->hasDefault('_controller') &&
      $route->hasDefault('_perform_operation');
  }

  /**
   * Update defaults for an entity perform operation.
   *
   * @param array $defaults
   *   The defaults to modify.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The Request instance.
   *
   * @return array
   *   The modified defaults.
   */
  protected function enhancePerformOperation(array $defaults, Request $request) {
    $defaults['_controller'] = EntityPlusController::class . '::performOperation';
    list($entity_type, $op) = explode('.', $defaults['_perform_operation']);
    $defaults['_entity'] = &$defaults[$entity_type];
    $defaults['_op'] = $op;

    unset($defaults['_perform_operation']);

    return $defaults;
  }

}
