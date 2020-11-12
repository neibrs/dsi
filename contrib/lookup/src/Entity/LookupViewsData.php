<?php

namespace Drupal\lookup\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Lookup entities.
 */
class LookupViewsData extends EntityViewsData {

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
