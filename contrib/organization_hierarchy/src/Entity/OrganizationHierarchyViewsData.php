<?php

namespace Drupal\organization_hierarchy\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Organization hierarchy entities.
 */
class OrganizationHierarchyViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['organization_hierarchy']['bulk_form'] = [
      'title' => $this->t('Operations bulk form'),
      'help' => $this->t('Add a form element that lets you run operations on multiple items.'),
      'field' => [
        'id' => 'bulk_form',
      ],
    ];

    return $data;
  }

}
