<?php

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;

/**
 * Implements hook_views_data().
 */
function entity_log_views_data() {
  $data = [];

  $data['entity_log']['table']['group'] = t('Entity log');

  $data['entity_log']['table']['base'] = [
    'field' => 'id',
    'title' => t('Entity Log'),
    'help' => t('Contains a list of entity log entries.'),
  ];

  $data['entity_log']['id'] = [
    'title' => t('ID'),
    'help' => t('Unique ID.'),
    'field' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['entity_log']['uid'] = [
    'title' => t('UID'),
    'help' => t('The user ID of the user on which the log entry was written.'),
    'field' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
    'relationship' => [
      'title' => t('User'),
      'help' => t('The user on which the log entry as written.'),
      'base' => 'users_field_data',
      'base field' => 'uid',
      'id' => 'standard',
    ],
  ];

  $data['entity_log']['entity_type_id'] = [
    'title' => t('Type'),
    'help' => t('The type of the log entry.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['entity_log']['entity_id'] = [
    'title' => t('Entity'),
    'help' => t('The entity on which the log entry was written.'),
    'field' => [
      'id' => 'standard',
    ],
    'filter' => [
      'id' => 'numeric',
    ],
    'argument' => [
      'id' => 'numeric',
    ],
  ];

  $entity_types = \Drupal::entityTypeManager()->getDefinitions();
  foreach ($entity_types as $entity_type_id => $entity_type) {
    if (!($entity_type instanceof ContentEntityTypeInterface)) {
      continue;
    }

    $data['entity_log'][$entity_type_id] = [
      'relationship' => [
        'title' => $entity_type->getLabel(),
        'help' => t('The @entity_type to which the log is reply to.', ['@entity_type' => $entity_type->getLabel()]),
        'base' => $entity_type->getDataTable() ?: $entity_type->getBaseTable(),
        'base field' => $entity_type->getKey('id'),
        'relationship field' => 'entity_id',
        'id' => 'standard',
        'label' => $entity_type->getLabel(),
        'extra' => [
          [
            'field' => 'entity_type_id',
            'value' => $entity_type_id,
            'table' => 'entity_log',
          ],
        ],
      ],
    ];
  }

  $data['entity_log']['message'] = [
    'title' => t('Message'),
    'help' => t('The actual message of the log entry.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['entity_log']['hostname'] = [
    'title' => t('Hostname'),
    'help' => t('Hostname of the user who triggered the event.'),
    'field' => [
      'id' => 'standard',
    ],
    'argument' => [
      'id' => 'string',
    ],
    'filter' => [
      'id' => 'string',
    ],
    'sort' => [
      'id' => 'standard',
    ],
  ];

  $data['entity_log']['timestamp'] = [
    'title' => t('Timestamp'),
    'help' => t('Date when the event occurred.'),
    'field' => [
      'id' => 'date',
    ],
    'argument' => [
      'id' => 'date',
    ],
    'filter' => [
      'id' => 'date',
    ],
    'sort' => [
      'id' => 'date',
    ],
  ];

  return $data;
}