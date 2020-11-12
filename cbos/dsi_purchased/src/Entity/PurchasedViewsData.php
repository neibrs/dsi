<?php

namespace Drupal\dsi_purchased\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Purchased entities.
 */
class PurchasedViewsData extends EntityViewsData {

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
