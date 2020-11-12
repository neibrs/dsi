<?php

namespace Drupal\organization_hierarchy\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\entity_plus\Entity\EffectiveDatesEntityBase;

/**
 * Defines the Organization hierarchy entity.
 *
 * @ingroup organization_hierarchy
 *
 * @ContentEntityType(
 *   id = "organization_hierarchy",
 *   label = @Translation("Organization hierarchy"),
 *   handlers = {
 *     "storage" = "Drupal\organization_hierarchy\OrganizationHierarchyStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\organization_hierarchy\OrganizationHierarchyListBuilder",
 *     "views_data" = "Drupal\organization_hierarchy\Entity\OrganizationHierarchyViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\organization_hierarchy\Form\OrganizationHierarchyForm",
 *       "add" = "Drupal\organization_hierarchy\Form\OrganizationHierarchyForm",
 *       "edit" = "Drupal\organization_hierarchy\Form\OrganizationHierarchyForm",
 *       "delete" = "Drupal\organization_hierarchy\Form\OrganizationHierarchyDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm"
 *     },
 *     "access" = "Drupal\organization_hierarchy\OrganizationHierarchyAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\organization_hierarchy\OrganizationHierarchyHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "organization_hierarchy",
 *   admin_permission = "maintain organizations",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/organization/hierarchy/{organization_hierarchy}",
 *     "add-form" = "/organization/hierarchy/add",
 *     "edit-form" = "/organization/hierarchy/{organization_hierarchy}/edit",
 *     "delete-form" = "/organization/hierarchy/{organization_hierarchy}/delete",
 *     "delete-multiple-form" = "/organization/hierarchy/delete",
 *     "collection" = "/organization/hierarchy",
 *   },
 *   field_ui_base_route = "organization_hierarchy.settings",
 *   effective_dates_entity = TRUE,
 * )
 */
class OrganizationHierarchy extends EffectiveDatesEntityBase implements OrganizationHierarchyInterface {

  /**
   * {@inheritdoc}
   */
  public function getName() {
    return $this->get('name')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setName($name) {
    $this->set('name', $name);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setSetting('max_length', 32)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => -40,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -40,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['version'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Version'))
      ->setSetting('max_length', 16)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => -20,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['organization'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Organization'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'organization')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => -10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -10,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['subordinates'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Subordinates'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'organization')
      // entity_reference_table formatter 需要 target_bundles.
      ->setSetting('handler_settings', [
        'target_bundles' => ['department', 'company'],
      ])
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_table',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'weight' => 0,
        'settings' => [
          'allow_new' => FALSE,
          'allow_existing' => TRUE,
          'match_operator' => 'CONTAINS',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    $items = [];
    if (!empty($this->getName())) {
      $items[] = $this->getName();
    }
    if (!empty($this->version->value)) {
      $items[] = $this->version->value;
    }
    if (!empty($this->getOrganization())) {
      $items[] = $this->getOrganization()->label();
    }

    return implode('-', $items);
  }

  /**
   * {@inheritdoc}
   */
  public function getOrganization() {
    return $this->get('organization')->entity;
  }
}
