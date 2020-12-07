<?php

namespace Drupal\dsi_contract\Entity;

use Drupal\organization\Entity\MultipleOrganizationEntityViewsData;

/**
 * Provides Views data for Contract entities.
 */
class ContractViewsData extends MultipleOrganizationEntityViewsData {

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
