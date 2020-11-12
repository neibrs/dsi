<?php

namespace Drupal\entity_plus;

use Drupal\reference_table_formatter\EntityToTableRenderer as EntityToTableRendererBase;

class EntityToTableRenderer extends EntityToTableRendererBase {

  protected function getDisplayRenderer($type, $bundle, $view_mode) {
    // TODO: Change the autogenerated stub
    $renderer = parent::getDisplayRenderer($type, $bundle, $view_mode);

    // Let the display know which view mode was originally requested.
    // Cannot access protected property Drupal\entity_plus\Entity\Entity\LayoutBuilderEntityViewDisplay::$originalMode
    // $renderer->originalMode = $view_mode;

    // Let modules alter the display.
    $display_context = [
      'entity_type' => $type,
      'bundle' => $bundle,
      'view_mode' => $view_mode,
    ];
    \Drupal::moduleHandler()->alter('entity_view_display', $renderer, $display_context);

    return $renderer;
  }

}
