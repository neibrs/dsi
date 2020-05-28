<?php

namespace Drupal\business_model\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\ContentEntityTypeInterface;
use Drupal\Core\Url;

/**
 * Returns responses for Business model routes.
 */
class BusinessModelController extends ControllerBase {

  public function entityTypeCollection() {
    $header = [
      'name' => $this->t('Name'),
      'id' => $this->t('ID'),
      'config' => $this->t('Config'),
      'operations' => $this->t('Operations'),
    ];

    $rows = [];
    $entity_types = $this->entityTypeManager()->getDefinitions();
    foreach ($entity_types as $entity_type) {
      $is_content_entity = ($entity_type instanceof ContentEntityTypeInterface);

      $row['name'] = $entity_type->getLabel();
      $row['id'] = $entity_type->id();
      $row['config'] = $is_content_entity ? '-' : $this->t('Yes');

      $operations = [];

      // Collection
      if ($entity_type->hasLinkTemplate('collection')) {
        $operations['list'] = [
          'title' => $this->t('List'),
          'url' => $this->ensureDestination(new Url('entity.' . $entity_type->id() . '.collection')),
        ];
      }

      // Manage fields
      if ($route_name = $entity_type->get('field_ui_base_route')) {
        $operations['field_ui'] = [
          'title' => $this->t('Manage fields'),
        ];
        if ($bundle_entity_type = $entity_type->getBundleEntityType()) {
          $operations['field_ui']['url'] = $this->ensureDestination(new Url('entity.' . $bundle_entity_type . '.collection'));
        }
        else {
          $operations['field_ui']['url'] = $this->ensureDestination(new Url($route_name));
        }
      }

      // Views management
      if ($is_content_entity) {
        $operations['views'] = [
          'title' => $this->t('Views'),
          'url' => $this->ensureDestination(new Url('business_model.entity_type.views', [
            'entity_type_id' => $entity_type->id(),
          ])),
        ];
      }

      // Data model
      if ($is_content_entity) {
        $operations['reference'] = [
          'title' => $this->t('Reference entity'),
          'url' => $this->ensureDestination(new Url('data_model.reference', [
            'entity_type_id' => $entity_type->id(),
          ])),
        ];
      }
      $operations['referenced_by'] = [
        'title' => $this->t('Referenced by'),
        'url' => $this->ensureDestination(new Url('data_model.referenced_by', [
          'entity_type_id' => $entity_type->id(),
        ])),
      ];

      // Workflows
      $workflow_storage = $this->entityTypeManager()->getStorage('workflow');
      if ($is_content_entity) {
        $fields = $this->entityManager()->getFieldStorageDefinitions($entity_type->id());
        foreach ($fields as $field) {
          if ($field->getType() == 'entity_status') {
            $workflow_id = $field->getSetting('workflow');
            $workflow = $workflow_storage->load($workflow_id);
            $operations['workflow_' . $workflow_id] = [
              'title' => $this->t('Workflow: %label', ['%label' => $workflow->label()]),
              'url' => $this->ensureDestination(new Url('entity.workflow.edit_form', ['workflow' => $workflow_id])),
            ];
          }
        }
      }

      $row['operations']['data'] = [
        '#type' => 'operations',
        '#links' => $operations,
      ];

      $rows[$entity_type->id()] = $row;
    }

    $build['table'] = [
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $rows,
    ];

    return $build;
  }

  protected function ensureDestination(Url $url) {
    return $url->mergeOptions(['query' => $this->getRedirectDestination()->getAsArray()]);
  }

  public function moduleCollection() {

  }

}
