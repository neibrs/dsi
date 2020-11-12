<?php

namespace Drupal\entity_plus\Entity\Enhancer;

use Drupal\Core\Entity\Enhancer\EntityRouteEnhancer as EntityRouteEnhancerBase;
use Symfony\Component\HttpFoundation\Request;

class EntityRouteEnhancer extends EntityRouteEnhancerBase {

  protected function enhanceEntityList(array $defaults, Request $request) {
    $defaults = parent::enhanceEntityList($defaults, $request);

    // Provides entity_list alter.
    $defaults['_controller'] = '\Drupal\entity_plus\Entity\Controller\EntityListController::listing';

    return $defaults;
  }

  protected function enhanceEntityView(array $defaults, Request $request) {
    $defaults = parent::enhanceEntityView($defaults, $request);

    // Remove the buildTitle pre_render.
    $defaults['_controller'] = '\Drupal\entity_plus\Entity\Controller\EntityViewController::view';

    return $defaults;
  }

}
