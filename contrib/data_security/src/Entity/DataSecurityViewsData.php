<?php

namespace Drupal\data_security\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Data securities.
 */
class DataSecurityViewsData extends EntityViewsData {

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
