<?php

namespace Drupal\person\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Identification information entities.
 */
class IdentificationInformationViewsData extends EntityViewsData {

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
