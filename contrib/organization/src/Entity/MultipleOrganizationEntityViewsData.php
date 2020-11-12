<?php

namespace Drupal\organization\Entity;

use Drupal\views_plus\EntityViewsData;

class MultipleOrganizationEntityViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $table = $this->entityType->getDataTable() ?: $this->entityType->getBaseTable();
    $data[$table]['table']['base']['access query tag'] = 'multiple_organization_access';
    $data[$table]['table']['base']['query metadata']['multiple_organization_entity'] = $this->entityType;

    return $data;
  }

}
