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
    $data['dsi_client_field_data']['phone'] = [
      'title' => $this->t('Client phone'),
      'help' => $this->t('Client: person or organization contact cell number.'),
      'field' => [
        'id' => 'cell_phone',
        'real field' => 'entity_id',
      ],
    ];

    $entities_types = \Drupal::entityTypeManager()->getDefinitions();
    foreach ($entities_types as $key => $types) {
      if (!in_array($key, ['person', 'organization'])) {
        unset($entities_types[$key]);
        continue;
      }
    }
    $client_types = \Drupal::entityTypeManager()->getStorage('dsi_client_type')->loadMultiple();

    foreach ($entities_types as $type => $entity_type) {
      foreach ($client_types as $key => $client_type) {
        if (empty($client_type->getTargetEntityTypeId())) {
          continue;
        }
        $data['dsi_client_field_data'][$type] = [
          'relationship' => [
            'title' => $entity_type->getLabel(),
            'help' => $this->t('The @entity_type to which the client.', ['@entity_type' => $entity_type->getLabel()]),
            'base' => $entity_type->getDataTable() ?: $entity_type->getBaseTable(),
            'base field' => $entity_type->getKey('id'),
            'relationship field' => 'entity_id',
            'id' => 'standard',
            'label' => $entity_type->getLabel(),
            'extra' => [
              [
                'field' =>'entity_type',
                'value' => $client_type->getTargetEntityBundle(),
                'table' => 'dsi_client_field_data',
              ],
            ],
          ],
        ];
      }
    }

    return $data;
  }

}
