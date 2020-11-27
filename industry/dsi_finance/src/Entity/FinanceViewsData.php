<?php

namespace Drupal\dsi_finance\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Finance entities.
 */
class FinanceViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    $data['dsi_finance_field_data']['entity_id']['field']['id'] = 'finance_sign';
    return $data;
  }

}
