<?php

namespace Drupal\organization\Entity;

/**
 * Provides Views data for organizations.
 */
class OrganizationViewsData extends MultipleOrganizationEntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['organization_field_data']['status']['filter']['type'] = 'yes-no';

    return $data;
  }

}
