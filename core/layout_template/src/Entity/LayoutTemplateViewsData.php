<?php

namespace Drupal\layout_template\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Layout template entities.
 */
class LayoutTemplateViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.

    return $data;
  }

}
