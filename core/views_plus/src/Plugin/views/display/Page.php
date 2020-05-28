<?php

namespace Drupal\views_plus\Plugin\views\display;

use Drupal\views\Plugin\views\display\Page as PageBase;
use Symfony\Component\Routing\Route;

class Page extends PageBase {

  public function getRoute($view_id, $display_id) {
    $route = parent::getRoute($view_id, $display_id);

    // Provides views_plus_view alter.
    $route->setDefault('_controller', 'Drupal\views_plus\Controller\ViewPageController::handle');

    return $route;
  }

}
