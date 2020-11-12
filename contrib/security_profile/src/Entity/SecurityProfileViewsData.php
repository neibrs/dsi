<?php

namespace Drupal\security_profile\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Security profile entities.
 */
class SecurityProfileViewsData extends EntityViewsData {

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
