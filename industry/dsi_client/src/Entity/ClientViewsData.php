<?php

namespace Drupal\dsi_client\Entity;

use Drupal\organization\Entity\MultipleOrganizationEntityViewsData;

/**
 * Provides Views data for Client entities.
 */
class ClientViewsData extends MultipleOrganizationEntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    $data['dsi_client_field_data']['entity_id']['field']['id'] = 'client_sign';
    return $data;
  }

}
