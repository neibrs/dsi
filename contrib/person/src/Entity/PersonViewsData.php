<?php

namespace Drupal\person\Entity;

use Drupal\organization\Entity\MultipleOrganizationEntityViewsData;

/**
 * Provides Views data for persons.
 */
class PersonViewsData extends MultipleOrganizationEntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['person_field_data']['status']['filter']['type'] = 'yes-no';
    $data['person_field_data']['type']['filter']['id'] = 'person_type';
    $data['person_field_data']['completion_of_probation_date']['filter']['allow empty'] = TRUE;
    $data['person_field_data']['birth_date']['filter']['allow empty'] = TRUE;
    $data['person_field_data']['adjusted_service_date']['filter']['allow empty'] = TRUE;
    $data['person_field_data']['hire_date']['filter']['allow empty'] = TRUE;
    $data['person_field_data']['rehire_date']['filter']['allow empty'] = TRUE;
    $data['person_field_data']['termination_actual_date']['filter']['allow empty'] = TRUE;
    $data['person_field_data']['termination_notified_date']['filter']['allow empty'] = TRUE;
    $data['person_field_data']['termination_projected_date']['filter']['allow empty'] = TRUE;
    
    // 为试用期提供更好的 filter.
    $data['person_field_data']['probation_period__value']['filter']['id'] = 'effective_dates';
    // effective_dates 插件会判断 value 和 end_value，所以删除 probation_period__end_value filter.
    unset($data['person_field_data']['probation_period__end_value']['filter']);
    
    // probation_period : value 视图字段插件包含了 end_value ,可删除 end_value 的视图字段插件.
    unset($data['person_field_data']['probation_period__end_value']['field']);
  
    $data['person_field_data']['probation_period__start'] = [
      'title' => $this->t('Probation period start'),
      'help' => $this->t('Probation period start'),
      'filter' => [
        'id' => 'datetime',
        'real field' => 'probation_period__value',
        'field_name' => 'probation_period',
      ],
      'field' => [
        'id' => 'date',
        'real field' => 'probation_period__value'
      ],
    ];
    $data['person_field_data']['probation_period__end'] = [
      'title' => $this->t('Probation period end'),
      'help' => $this->t('Probation period end'),
      'filter' => [
        'id' => 'datetime',
        'real field' => 'probation_period__end_value',
        'field_name' => 'probation_period',
      ],
      'field' => [
        'id' => 'date',
        'real field' => 'probation_period__end_value'
      ],
    ];

    $data['person_field_data']['age'] = [
      'title' => $this->t('Age'),
      'filter' => [
        'field' => 'birth_date',
        'id' => 'person_age',
        'label' => $this->t('Age'),
      ],
      'field' => [
        'field' => 'birth_date',
        'id' => 'person_age',
      ],
    ];

    $data['person_field_data']['length_of_service'] = [
      'title' => $this->t('Length of service'),
      'filter' => [
        'field' => 'adjusted_service_date',
        'id' => 'person_age',
        'label' => $this->t('Length of service'),
      ],
      'field' => [
        'field' => 'adjusted_service_date',
        'id' => 'person_age',
      ],
    ];

    $data['person_field_data']['users'] = [
      'title' => $this->t('Accounts'),
      'help' => $this->t("Person's user accounts."),
      'field' => [
        'id' => 'person_users',
      ],
    ];

    return $data;
  }

}
