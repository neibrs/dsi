<?php

namespace Drupal\person\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntity;

/**
 * Defines the Person entity.
 *
 * @ingroup person
 *
 * @ContentEntityType(
 *   id = "person",
 *   label = "人员",
 *   label_collection = @Translation("Persons"),
 *   bundle_label = @Translation("Person type"),
 *   handlers = {
 *     "storage" = "Drupal\person\PersonStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\person\PersonListBuilder",
 *     "views_data" = "Drupal\person\Entity\PersonViewsData",
 *     "translation" = "Drupal\person\PersonTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\person\Form\PersonForm",
 *       "add" = "Drupal\person\Form\PersonForm",
 *       "edit" = "Drupal\person\Form\PersonForm",
 *       "delete" = "Drupal\person\Form\PersonDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm",
 *     },
 *     "access" = "Drupal\person\PersonAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\person\PersonHtmlRouteProvider",
 *     },
 *     "inline_form" = "Drupal\person\Form\PersonInlineForm",
 *   },
 *   base_table = "person",
 *   data_table = "person_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer persons",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *     "organization" = "organization",
 *   },
 *   links = {
 *     "canonical" = "/person/{person}",
 *     "add-page" = "/person/add",
 *     "add-form" = "/person/add/{person_type}",
 *     "edit-form" = "/person/{person}/edit",
 *     "delete-form" = "/person/{person}/delete",
 *     "delete-multiple-form" = "/person/delete",
 *     "collection" = "/person",
 *   },
 *   bundle_entity_type = "person_type",
 *   field_ui_base_route = "entity.person_type.edit_form",
 *   common_reference_target = TRUE,
 *   multiple_organization_classification = "business_group",
 *   effective_dates_entity = TRUE,
 *   match_fields = {"number", "name", "pinyin"},
 * )
 */
class Person extends EffectiveDatesBusinessGroupEntity implements PersonInterface {

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
  public function getOrganization() {
    return $this->get('organization')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOrganizationByClassification($classification) {
    if ($organization = $this->getOrganization()) {
      return $organization->getByClassification($classification);
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['number'] = BaseFieldDefinition::create('code')
      ->setLabel(t('Number', [], ['context' => 'Person']))
      ->setSetting('max_length', 32)
      ->setSetting('encoding_rules', \Drupal::config('person.settings')->get('encoding_rules'))
      // TODO: 改到表单校验 ->addConstraint('MultiOrganizationUniqueField')
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => -30,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -30,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name', [], ['context' => 'Person']))
      ->setRequired(TRUE)
      ->setSetting('max_length', 32)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => -20,
        'label' => 'inline',
        'settings' => [
          'link_to_entity' => TRUE,
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['pinyin'] = BaseFieldDefinition::create('pinyin_shortcode')
      ->setLabel(t('Pinyin shortcode'))
      ->setSetting('source_field', 'name');

    $fields['type']
			->setLabel(t('Person type'))
      ->setDefaultValue('employee')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => -10,
        'label' => 'inline',
        'settings' => [
          'link' => FALSE,
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['people_group'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Person group'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['people_group' => 'people_group'],
        'auto_create' => TRUE,
      ])
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

   $fields['identify_number'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Identify number'))
      ->setSetting('max_length', 32)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['identification_information'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Identification information'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'identification_information')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'settings' => [
          'form_mode' => 'inline_entity_form',
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // GB_T2261_1 人的性别代码
    $fields['gender'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Gender'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['gender' => 'gender'],
      ])
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

    // TODO title???

    /**
     * 个人信息：
     * birth_date(出生日期) 年龄
     * 祖籍
     * 出生地点
     * 出生国家(地区)
     * 婚姻状态
     * nationality(国籍)
     * 登记的伤残人员
     */

    $fields['birth_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Birth date'))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setDisplayOptions('view', [
        'type' => 'datetime_default',
        'weight' => 0,
        'label' => 'inline',
        'settings' => [
          'format_type' => 'html_date',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // GB_T2261_2婚姻状况代码
    $fields['marital_status'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Marital status'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['marital_status' => 'marital_status'],
      ])
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

    // GB_T3304 民族类别代码
    $fields['nationality'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Nationality'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['nationality' => 'nationality'],
      ])
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

    // 引自GB_T2260_2018 行政区划字典
    $fields['native_place'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Native place'))
      ->setSetting('target_type', 'administrative_area')
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

    /**
     * 其他个人信息：
     * 户口类型
     * 户口所在地
     * 名族/种族血统
     * 社会保险IC编号
     */

    $fields['organization'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Organization'))
      ->setSetting('target_type', 'organization')
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

    // Benefits information
    // 用于工龄计算
    $fields['adjusted_service_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Adjusted service date',[],['context' => 'Benefits']))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setDisplayOptions('view', [
        'type' => 'datetime_default',
        'weight' => 0,
        'label' => 'inline',
        'settings' => [
          'date_format' => 'html_date',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['picture'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Pictures'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('view', [
        'type' => 'image',
        'weight' => 100,
        'label' => 'hidden',
        'settings' => [
          'image_style' => '80x',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 100,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    if ($uuid = \Drupal::state()->get('default_person_picture_uuid')) {
      $fields['picture']->setSetting('default_image', [
        'uuid' => $uuid,
        'alt' => t('Person photo'),
        'title' => t('Person photo'),
        'width' => 610,
        'height' => 610,
      ]);
    }

    $fields['attachments'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Attachments'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('file_extensions', 'doc docx xls xlsx jpeg png txt')
      ->setDisplayOptions('view', [
        'type' => 'file_default',
        'weight' => 110,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'file_generic',
        'weight' => 110,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Employee status
    $fields['employee_status'] = BaseFieldDefinition::create('entity_status')
      ->setLabel(t('Employee status'))
      ->setRequired(TRUE)
      ->setSetting('workflow_type', 'employee_status')
      ->setSetting('workflow', 'default_employee_status')
      ->setSetting('ignore_transitions', TRUE)
      ->setDefaultValue('active')
      ->setDisplayOptions('view', [
        'type' => 'list_default',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Employee field.

    $fields['hire_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Hire date'))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setDisplayOptions('view', [
        'type' => 'datetime_default',
        'weight' => 0,
        'label' => 'inline',
        'settings' => [
          'format_type' => 'html_date',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['rehire_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Rehire date'))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setDisplayOptions('view', [
        'type' => 'datetime_default',
        'weight' => 0,
        'label' => 'inline',
        'settings' => [
          'format_type' => 'html_date',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    
    $fields['probation_period'] = BaseFieldDefinition::create('daterange')
      ->setLabel(t('Probation period'))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setSetting('optional_start_date', TRUE)
      ->setSetting('optional_end_date', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'daterange_default',
        'weight' => 80,
        'label' => 'inline',
        'settings' => [
          'format_type' => 'html_date',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'daterange_default',
        'weight' => 80,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 转正信息
    // TODO: completion_of_probation_date_reason
    
    $fields['completion_of_probation_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Completion of probation date'))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setDisplayOptions('view', [
        'type' => 'datetime_default',
        'weight' => 0,
        'label' => 'inline',
        'settings' => [
          'format_type' => 'html_date',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Contact information: Address, Phone, Email.

    $fields['address'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Address'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'person_address')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'settings' => [
          'form_mode' => 'inline_entity_form',
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
  
    $fields['phone'] = BaseFieldDefinition::create('telephone')
      ->setLabel(t('Phone'))
      ->setSettings([
        'max_length' => 20,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'type' => 'basic_string',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'telephone_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['email'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Email'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'person_email')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'settings' => [
          'form_mode' => 'inline_entity_form',
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    // entity_keys 要求 organization 字段必填.
    if (empty($this->getOrganization())) {
      $this->organization->target_id = 0;
    }

    // 自动转移hire_date到adjusted_service_date
    if (empty($this->adjusted_service_date->value)) {
      $this->adjusted_service_date->value = $this->hire_date->value;
    }

    parent::preSave($storage);
  }

  /**
   * {@inheritdoc}
   */
  public static function postDelete(EntityStorageInterface $storage, array $entities) {
    foreach ($entities as $entity) {
      foreach (['phone', 'email', 'address'] as $field_name) {
        foreach ($entity->$field_name as $field) {
          $field->entity->delete();
        }
      }
    }

    parent::postDelete($storage, $entities);
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    // Ensure the back-reference.
    foreach (['address', 'phone', 'email', 'identification_information'] as $field_name) {
      foreach ($this->$field_name as $field) {
        if (empty($field->target_id)) {
          continue;
        }
        $target_entity = $field->entity;
        if ($target_entity->person->isEmpty() || $target_entity->person->target_id != $this->id()) {
          $target_entity->person->target_id = $this->id();
          $target_entity->save();
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getOperatingUnits() {
    $organizations = [];

    if ($current_operating_unit = $this->getOrganizationByClassification('operating_unit')) {
      $organizations[$current_operating_unit->id()] = $current_operating_unit;
    }

    // TODO add assigned operating units.
    return $organizations;
  }

  /**
   * {@inheritdoc}
   */
  public function getType() {
    return $this->get('type')->entity;
  }
  
  /**
   * {@inheritdoc}
   */
  public function getUserId() {
    $ids = \Drupal::entityTypeManager()->getStorage('user')
      ->getQuery()
      ->condition('person', $this->id())
      ->condition('status', TRUE)
      ->execute();
    return reset($ids);
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    $items = [];
    if (!empty($this->get('number')->value)) {
      $items[] = $this->get('number')->value;
    }
    if (!empty($this->getName())) {
      $items[] = $this->getName();
    }

    return implode('-', $items);
  }
  
  /**
   * {@inheritdoc}
   */
  public function getPrimaryPhone() {
    foreach ($this->phone->referencedEntities() as $entity) {
      if ($entity->primary->value) {
        return $entity->name->value;
      }
    }
  }
  
  /**
   * {@inheritdoc}
   */
  public function getPrimaryEmail() {
    foreach ($this->email->referencedEntities() as $entity) {
      if ($entity->primary->value) {
        return $entity->name->value;
      }
    }
  }
}
