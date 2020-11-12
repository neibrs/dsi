<?php

namespace Drupal\role_frontpage\Controller;

use Drupal\Core\Controller\ControllerBase;

class RoleFrontpageController extends ControllerBase {

  /**
   * Displays the workbench page.
   *
   * @return array
   *   An array suitable for \Drupal\Core\Render\RendererInterface::render().
   */
  public function workbench() {
    $elements = [
      '#theme' => 'workbench',
    ];

    $this->moduleHandler()->invokeAll('workbench_view', [&$elements]);

    return $elements;
  }

}
