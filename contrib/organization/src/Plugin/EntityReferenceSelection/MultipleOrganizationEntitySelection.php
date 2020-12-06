<?php

namespace Drupal\organization\Plugin\EntityReferenceSelection;

use Drupal\Core\Session\AnonymousUserSession;
use Drupal\entity_plus\Plugin\EntityReferenceSelection\DefaultSelection;

class MultipleOrganizationEntitySelection extends DefaultSelection {

  /**
   * {@inheritdoc}
   * TODO, employee assignment not exist.
   */
  protected function buildEntityQuery($match = NULL, $match_operator = 'CONTAINS') {
    $query = parent::buildEntityQuery($match, $match_operator);

    // By pass multiple organization access.
    $user = \Drupal::currentUser();
    if ($user->hasPermission('bypass multiple organization access') || $user instanceof AnonymousUserSession) {
      return $query;
    }

    // 获得多组织查询参数.
    $entity_type = \Drupal::entityTypeManager()->getDefinition($this->configuration['target_type']);
    $query_field_prefix = '';
    list($target_entity_type_id, $multiple_organization_classification, $query_field_prefix) = $this->buildMultipleOrganizationQueryParameter($entity_type->id(), $query_field_prefix);

    // 如果不是多组织实体，直接返回.
    if (empty($target_entity_type_id)) {
      return $query;
    }

    $target_entity_type = \Drupal::entityTypeManager()->getDefinition($target_entity_type_id);

    /** @var \Drupal\person\Entity\PersonInterface $person */
    if (\Drupal::moduleHandler()->moduleExists('person') && $person = \Drupal::service('person.manager')->currentPerson()) {
      $organization = \Drupal::service('person.manager')->currentPersonOrganizationByClassification($multiple_organization_classification);
    }
    if (isset($organization)) {
      $organizations = \Drupal::service('person.manager')->currentPersonAccessibleOrganizationByClassification($multiple_organization_classification);
      $organizations = array_keys($organizations);

      // organization实体需要特殊处理.
      if ($this->configuration['target_type'] == 'organization' && $multiple_organization_classification == 'business_group') {
        $query->condition($query->orConditionGroup()
          ->condition(empty($query_field_prefix) ? 'business_group' : "${query_field_prefix}.entity.business_group", $organizations, 'IN')
          ->condition(empty($query_field_prefix) ? 'id' : $query_field_prefix . '.entity.id', $organizations, 'IN')
        );
      }
      // 普通多组织实体处理.
      else {
        $parent_organizations = [];
        if ($master_field = $target_entity_type->getKey('master')) {
          $parent_organizations = \Drupal::entityTypeManager()
            ->getStorage('organization')
            ->loadParentsByClassification($organization, $multiple_organization_classification);
          $parent_organizations = array_keys($parent_organizations);
        }
        // 如果需要进行主数据管理(Master data)，并且有上级多组织.
        if (!empty($parent_organizations)) {
          $query->condition($query->orConditionGroup()
            ->condition(empty($query_field_prefix) ? $multiple_organization_classification : "${query_field_prefix}.entity.${multiple_organization_classification}", $organizations, 'IN')
            ->condition($query->andConditionGroup()
              ->condition(empty($query_field_prefix) ? 'master' : "${query_field_prefix}.entity.master", TRUE)
              ->condition(empty($query_field_prefix) ? $multiple_organization_classification : "${query_field_prefix}.entity.${multiple_organization_classification}", $parent_organizations, 'IN')
            )
          );
        }
        else {
          $query->condition(empty($query_field_prefix) ? $multiple_organization_classification : "${query_field_prefix}.entity.${multiple_organization_classification}", $organizations, 'IN');
        }
      }
    }
    else {
      $query->condition('id', null);
    }

    return $query;
  }

  protected function buildMultipleOrganizationQueryParameter($entity_type_id, $query_field_prefix) {
    $target_entity_type_id = $entity_type_id;

    $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);
    if ($multiple_organization_classification = $entity_type->get('multiple_organization_classification')) {
      return [$target_entity_type_id, $multiple_organization_classification, $query_field_prefix];
    }

    $multiple_organization_field = $entity_type->get('multiple_organization_field');
    if (!$multiple_organization_field) {
      return [NULL, NULL, NULL];
    }

    if (!empty($query_field_previx)) {
      $query_field_prefix .= '.entity.';
    }
    $query_field_prefix .= $multiple_organization_field;

    $field_storage_definitions = \Drupal::service('entity_field.manager')->getFieldStorageDefinitions($entity_type_id);
    $field_storage_definition = $field_storage_definitions[$multiple_organization_field];
    $target_entity_type_id = $field_storage_definition->getSetting('target_type');
    return $this->buildMultipleOrganizationQueryParameter($target_entity_type_id, $query_field_prefix);
  }
}
