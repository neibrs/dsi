<?php

namespace Drupal\dsi_litigant\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Litigant entities.
 */
class LitigantViewsData extends EntityViewsData {

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
