<?php

namespace Drupal\dsi_cases\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Cases entities.
 */
class CasesViewsData extends EntityViewsData {

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
