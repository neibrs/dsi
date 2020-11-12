<?php

namespace Drupal\user_plus\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Permission sets.
 */
class PermissionSetViewsData extends EntityViewsData {

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
