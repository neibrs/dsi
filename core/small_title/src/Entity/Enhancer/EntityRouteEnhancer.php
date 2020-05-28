<?php

namespace Drupal\small_title\Entity\Enhancer;

use Drupal\Core\Entity\Enhancer\EntityRouteEnhancer as EntityRouteEnhancerBase;
use Symfony\Component\HttpFoundation\Request;

/**
 */
class EntityRouteEnhancer extends EntityRouteEnhancerBase {

  protected function enhanceEntityView(array $defaults, Request $request) {
    $defaults = parent::enhanceEntityView($defaults, $request);

    // Remove buildTitle from EntityViewController::view
    $defaults['_controller'] = '\Drupal\small_title\Entity\Controller\EntityViewController::view';

    return $defaults;
  }

}
