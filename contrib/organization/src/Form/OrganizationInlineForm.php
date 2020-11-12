<?php

namespace Drupal\organization\Form;

use Drupal\entity_plus\Form\EntityInlineForm;

class OrganizationInlineForm extends EntityInlineForm {

  /**
   * {@inheritdoc}
   */
  public function getTableFields($bundles) {
    $fields = [];

    $fields['label'] = [
      'type' => 'label',
      'label' => t('Organization'),
      'weight' => 1,
    ];
    $fields['description'] = [
      'type' => 'field',
      'label' => t('Description'),
      'weight' => 2,
    ];
    $fields['location'] = [
      'type' => 'field',
      'label' => t('Location'),
      'weight' => 3,
      'display_options' => [
        'type' => 'entity_reference_label',
        'settings' => ['link' => FALSE],
      ],
    ];

    return $fields;
  }

}
