<?php

namespace Drupal\user_plus\Entity;

use Drupal\Core\Database\Query\Condition;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntity;

/**
 * Defines the Permission set entity.
 *
 * @ingroup user_plus
 *
 * @ContentEntityType(
 *   id = "permission_set",
 *   label = @Translation("Permission set"),
 *   label_collection = @Translation("Permission sets"),
 *   handlers = {
 *     "view_builder" = "Drupal\user_plus\PermissionSetViewBuilder",
 *     "list_builder" = "Drupal\user_plus\PermissionSetListBuilder",
 *     "views_data" = "Drupal\user_plus\Entity\PermissionSetViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\user_plus\Form\PermissionSetForm",
 *       "add" = "Drupal\user_plus\Form\PermissionSetForm",
 *       "edit" = "Drupal\user_plus\Form\PermissionSetForm",
 *       "delete" = "Drupal\user_plus\Form\PermissionSetDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\user_plus\PermissionSetHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\user_plus\PermissionSetAccessControlHandler",
 *   },
 *   base_table = "permission_set",
 *   translatable = FALSE,
 *   admin_permission = "administer permission sets",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *     "master" = "master",
 *   },
 *   links = {
 *     "canonical" = "/permission_set/{permission_set}",
 *     "add-form" = "/permission_set/add",
 *     "edit-form" = "/permission_set/{permission_set}/edit",
 *     "delete-form" = "/permission_set/{permission_set}/delete",
 *     "collection" = "/permission_set",
 *   },
 *   field_ui_base_route = "permission_set.settings",
 *   multiple_organization_classification = "business_group",
 * )
 */
class PermissionSet extends EffectiveDatesBusinessGroupEntity implements PermissionSetInterface {

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
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['pinyin'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Pinyin shortcode'))
      ->setSetting('max_length', 64);

    $fields['code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Code', [], ['context' => 'Entity']))
      ->setSetting('max_length', 32)
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

    $fields['inherited_duties'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Inherited duties'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
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

    $fields['permissions'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Permissions'))
      ->setDefaultValue([]);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function getPermissions() {
    $permissions = $this->permissions->value;
    return $permissions ?: [];
  }

  /**
   * {@inheritdoc}
   */
  public function getAllPermissions() {
    $permissions = $this->getPermissions();

    $query = \Drupal::database()->select('permission_set__inherited_duties', 'id');
    $query->addField('id', 'inherited_duties_target_id');
    $where = (new Condition('OR'))->condition('id.entity_id', $this->id());
    $last = 'id';

    // 继承的职责允许嵌套5级.
    foreach (range(1, 5) as $count) {
      $query->leftJoin('permission_set__inherited_duties', "id$count", "$last.entity_id = id$count.inherited_duties_target_id");
      $where->condition("id$count.entity_id", $this->id());
      $last = "id$count";
    }

    $query->condition($where);
    $inherited_ids = $query->execute()->fetchCol();
    if (empty($inherited_ids)) {
      return $permissions;
    }

    $inherited_duties = $this->entityTypeManager()->getStorage('permission_set')->loadMultiple($inherited_ids);
    foreach ($inherited_duties as $inherited_duty) {
      $permissions += $inherited_duty->getPermissions();
    }

    return $permissions;
  }

  /**
   * {@inheritdoc}
   */
  public function setPermissions($permissions) {
    $this->permissions->value = $permissions;

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasPermission($permission) {
    return in_array($permission, $this->getAllPermissions());
  }

}
