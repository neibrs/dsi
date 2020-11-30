<?php

namespace Drupal\dsi_record\Entity;

use Drupal\alert\Entity\Alert;
use Drupal\alert\Entity\AlertType;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\user\UserInterface;

/**
 * Defines the Record entity.
 *
 * @ingroup dsi_record
 *
 * @ContentEntityType(
 *   id = "dsi_record",
 *   label = @Translation("Record"),
 *   label_collection = @Translation("Record"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_record\RecordListBuilder",
 *     "views_data" = "Drupal\dsi_record\Entity\RecordViewsData",
 *     "translation" = "Drupal\dsi_record\RecordTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\dsi_record\Form\RecordForm",
 *       "add" = "Drupal\dsi_record\Form\RecordForm",
 *       "edit" = "Drupal\dsi_record\Form\RecordForm",
 *       "delete" = "Drupal\dsi_record\Form\RecordDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_record\RecordHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\dsi_record\RecordAccessControlHandler",
 *   },
 *   base_table = "dsi_record",
 *   data_table = "dsi_record_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer record entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/dsi_record/{dsi_record}",
 *     "add-form" = "/dsi_record/add",
 *     "edit-form" = "/dsi_record/{dsi_record}/edit",
 *     "delete-form" = "/dsi_record/{dsi_record}/delete",
 *     "collection" = "/dsi_record",
 *   },
 *   field_ui_base_route = "dsi_record.settings"
 * )
 */
class Record extends ContentEntityBase implements RecordInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
    ];
  }

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
  public function getCreatedTime() {
    return $this->get('created')->value;
  }

  /**
   * {@inheritdoc}
   */
  public function setCreatedTime($timestamp) {
    $this->set('created', $timestamp);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwner() {
    return $this->get('user_id')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function getOwnerId() {
    return $this->get('user_id')->target_id;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwnerId($uid) {
    $this->set('user_id', $uid);
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function setOwner(UserInterface $account) {
    $this->set('user_id', $account->id());
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 5,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'autocomplete_type' => 'tags',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name', [], ['context' => 'Record']))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    // TODO, Add fields.
    // 工作详情
    $fields['detail'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Detail', [], ['context' => 'Record']))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 开始时间 - 结束时间
    $fields['start'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Start', [], ['context' => 'Record']))
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

    $fields['end'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('End', [], ['context' => 'Record']))
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

    // 提醒时间
    $fields['reminder'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Reminder time', [], ['context' => 'Record']))
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

    // 完成状态
    // TODO, state, reference entity.

    // 花费时间
    $fields['take_time'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Take time', [], ['context' => 'Record']))
      ->setSetting('unsigned', TRUE)
      ->setSetting('size', 'big')
      ->setDisplayOptions('view', [
        'type' => 'number_integer',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 附件
    $fields['attachments'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Attachments', [], ['context' => 'Record']))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('file_extensions', 'doc docx xls xlsx jpeg png txt')
      ->setDisplayOptions('view', [
        'type' => 'file_default',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'file_generic',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 出类设计图后再加
    // 所属团队
    // 所属律所
    $fields['entity_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Entity type'))
      ->setRequired(TRUE)
      ->setSetting('is_ascii', TRUE)
      ->setSetting('max_length', EntityTypeInterface::ID_MAX_LENGTH);

    $fields['entity_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Entity ID'))
      ->setDefaultValue(0);

    // 处理状态:
    // False, 未处理
    // True, 已处理
    $fields['state'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('State', [], ['context' => 'Record state']))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'boolean',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Record is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    $entity_type_id = $this->get('entity_type')->value;
    $entity_id = $this->get('entity_id')->value;
    if (!empty($entity_type_id) && !empty($entity_id)) {
      /** @var \Drupal\Core\Entity\EntityInterface $target_entity */
      $target_entity_type = $this->entityTypeManager()->getStorage($entity_type_id);
      $target_entity = $target_entity_type->load($entity_id);
      $target_entity->get('record')->appendItem($this);
      $target_entity->save();
    }
    else {
      return;
    }

    // Add alert
    if ($this->isNew()) {
      $alert_type = $this->entityTypeManager()->getStorage('alert_type')->load($entity_type_id);
      if (empty($alert_type)) {
        // Add alert type.
        $alert_type = AlertType::create([
          'id' => $entity_type_id,
          'name' => $target_entity_type->getEntityType()->getLabel(),
        ]);
        $alert_type->save();
      }
      // 创建alert数据.
      $alert = Alert::create([
        'name' => $this->label(),
        'type' => $entity_type_id,
      ]);
      $alert->save();
    }
  }

}
