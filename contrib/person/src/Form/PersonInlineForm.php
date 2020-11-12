<?php

namespace Drupal\person\Form;

use Drupal\entity_plus\Form\EntityInlineForm;

class PersonInlineForm extends EntityInlineForm {

  public function getTableFields($bundles) {
    $fields = [];

    $fields['name'] = [
      'type' => 'field',
      'label' => t('Name'),
      'weight' => 1,
    ];
    $fields['gender'] = [
      'type' => 'field',
      'label' => t('Gender'),
      'weight' => 2,
    ];

    return $fields;
  }

}
