<?php

namespace Drupal\grant\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntity;

/**
 * Defines the Grant entity.
 *
 * @ingroup grant
 *
 * @ContentEntityType(
 *   id = "grant",
 *   label = @Translation("Grant"),
 *   label_collection = @Translation("Grants"),
 *   bundle_label = @Translation("Grantee type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\grant\GrantListBuilder",
 *     "views_data" = "Drupal\grant\Entity\GrantViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\grant\Form\GrantForm",
 *       "add" = "Drupal\grant\Form\GrantForm",
 *       "edit" = "Drupal\grant\Form\GrantForm",
 *       "delete" = "Drupal\grant\Form\GrantDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\grant\GrantHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\grant\GrantAccessControlHandler",
 *   },
 *   base_table = "grant_table",
 *   translatable = FALSE,
 *   admin_permission = "administer grants",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "grantee_type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *     "master" = "master",
 *   },
 *   links = {
 *     "canonical" = "/grant/{grant}",
 *     "add-page" = "/grant/add",
 *     "add-form" = "/grant/add/{grant_type}",
 *     "edit-form" = "/grant/{grant}/edit",
 *     "delete-form" = "/grant/{grant}/delete",
 *     "collection" = "/grant",
 *   },
 *   bundle_entity_type = "grant_type",
 *   multiple_organization_classification = "business_group",
 * )
 */
class Grant extends EffectiveDatesBusinessGroupEntity implements GrantInterface {

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
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => -10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setSetting('max_length', 64)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    /*
     * Security context:
     * 1. Grantee type
     * 2. Grantee
     * 3. Operating unit
     * 4. Responsibility
     */

    $fields['grantee_type']
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['grantee'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Grantee'))
      ->setSetting('target_type', 'user_role');

    $fields['operating_unit'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Operating unit'))
      ->setSetting('target_type', 'organization')
      ->setSetting('handler_settings', [
        'conditions' => [
          'classifications' => 'operating_unit',
        ],
      ])
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['responsibility'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Responsibility'))
      ->setSetting('target_type', 'responsibility')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['set'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Set', [], ['context' => 'Permission']))
      ->setSetting('target_type', 'permission_set')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['data_security'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Data security'))
      ->setSetting('target_type', 'data_security')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public static function bundleFieldDefinitions(EntityTypeInterface $entity_type, $bundle, array $base_field_definitions) {
    if ($type = GrantType::load($bundle)) {
      // 有些 Grantee type，例如 all_users，不需要提供 target_entity_type_id.
      if ($target_entity_type_id = $type->getTargetEntityTypeId()) {
        $fields['grantee'] = clone $base_field_definitions['grantee'];
        $fields['grantee']
          ->setSetting('target_type', $target_entity_type_id)
          ->setDisplayOptions('view', [
            'type' => 'entity_reference_label',
            'weight' => 0,
            'label' => 'inline',
          ])
          ->setDisplayOptions('form', [
            'type' => 'entity_reference_autocomplete',
            'weight' => 0,
          ])
          ->setDisplayConfigurable('form', TRUE)
          ->setDisplayConfigurable('view', TRUE);
        // 对于可选数据不多的情况，采用下拉选择框.
        if ($target_entity_type_id == 'user_role') {
          $fields['grantee']
            ->setDisplayOptions('form', [
              'type' => 'options_select',
              'weight' => 0,
            ]);
        }
        return $fields;
      }
    }
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function getPermissionSet() {
    return $this->get('set')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getDataSecurity() {
    return $this->get('data_security')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function hasEntityPermission(EntityInterface $entity, $permission, AccountInterface $account = NULL) {
    // 检查权限.
    if (!$permission_set = $this->getPermissionSet()) {
      return FALSE;
    }
    if (!$permission_set->hasPermission($permission)) {
      return FALSE;
    }

    // 检查数据安区政策.
    if (!$data_security = $this->getDataSecurity()) {
      return TRUE;
    }
    if ($data_security->entity_type->value == $entity->getEntityTypeId()) {
      return $data_security->withinScope($entity->id());
    }

    return TRUE;
  }

}
