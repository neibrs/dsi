<?php

namespace Drupal\dsi_contract\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntity;
use Drupal\user\UserInterface;

/**
 * Defines the Contract entity.
 *
 * @ingroup dsi_contract
 *
 * @ContentEntityType(
 *   id = "dsi_contract",
 *   label = @Translation("Contract"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_contract\ContractListBuilder",
 *     "views_data" = "Drupal\dsi_contract\Entity\ContractViewsData",
 *     "translation" = "Drupal\dsi_contract\ContractTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\dsi_contract\Form\ContractForm",
 *       "add" = "Drupal\dsi_contract\Form\ContractForm",
 *       "edit" = "Drupal\dsi_contract\Form\ContractForm",
 *       "delete" = "Drupal\dsi_contract\Form\ContractDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_contract\ContractHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\dsi_contract\ContractAccessControlHandler",
 *   },
 *   base_table = "dsi_contract",
 *   data_table = "dsi_contract_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer contract entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/dsi_contract/{dsi_contract}",
 *     "add-form" = "/dsi_contract/add",
 *     "edit-form" = "/dsi_contract/{dsi_contract}/edit",
 *     "delete-form" = "/dsi_contract/{dsi_contract}/delete",
 *     "collection" = "/dsi_contract",
 *   },
 *   field_ui_base_route = "dsi_contract.settings",
 *   multiple_organization_field = "person",
 *   personal_owner = "person",
 * )
 */
class Contract extends EffectiveDatesBusinessGroupEntity implements ContractInterface {

  use EntityChangedTrait;

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
      ->setLabel(t('Name', [], ['context' => 'Contract title']))
      ->setDescription(t('The name of the Contract entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
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
    // 合同编号
    $fields['number'] = BaseFieldDefinition::create('code')
      ->setLabel(t('Number', [], ['context' => 'Contract']))
      ->setSetting('max_length', 32)
      ->setSetting('encoding_rules', \Drupal::config('dsi_contract.settings')->get('encoding_rules'))
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

    // 客户
    $fields['client'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Client', [], ['context' => 'Contract client']))
      ->setSetting('target_type', 'dsi_client')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 6,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 6,
        'settings' => [
          'match_operator' => 'CONTAINS',
          'size' => '60',
          'placeholder' => '',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 合同类型=案件类型
    $fields['case_category'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Case Category'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['case_category' => 'case_category'],
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

    // 委托人(合同委托人)
    $fields['consignor'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Consignor'))
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
      ->setDisplayConfigurable('view', TRUE);

    // 证件号码
    $fields['passport_number'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Passport Number'))
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
      ->setDisplayConfigurable('view', TRUE);

    // 是否为当事人
    $fields['is_litigant'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Is litigant'))
      ->setDefaultValue(TRUE)
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
        'settings' => [
          'format' => 'yes-no',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE);

    // 与当事人关系
    $fields['litigant_relationship'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Litigant Relationship'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['litigant_relationship' => 'litigant_relationship'],
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

    // 委托程序
    $fields['case_procedure'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Case procedure'))
      ->setSetting('target_type', 'lookup')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('handler_settings', [
        'target_bundles' => ['case_procedure' => 'case_procedure'],
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

    // 其他事项
    $fields['other_description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Other description'))
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
      ->setDisplayConfigurable('view', TRUE);

    // 授权类型
    $fields['auth_type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Auth Type'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['auth_type' => 'auth_type'],
      ])
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 合同金额
    $fields['amount'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Amount', [], ['context' => 'Contract']))
      ->setDisplayOptions('view', [
        'type' => 'number_decimal',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 金额大写
    $fields['amount_text'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Amount Text'))
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
      ->setDisplayConfigurable('view', TRUE);
    // 提成比例
    $fields['rate'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Rate', [], ['context' => 'Contract']))
      ->setDisplayOptions('view', [
        'type' => 'number_decimal',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    // 通讯地址
    $fields['address'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Address', [], ['context' => 'Contract']))
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
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    // 签定时间
    $fields['signing_time'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Signing Time', [], ['context' => 'Contract']))
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

    // 差旅费约定
    $fields['travel_expenses_agreement'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Travel expenses agreement'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['auth_type' => 'auth_type'],
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

    // 差旅费预收
    $fields['travel_expenses_collection'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Travel expenses collection', [], ['context' => 'Contract']))
      ->setDisplayOptions('view', [
        'type' => 'number_decimal',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 备注 || 合作内容
    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description'))
      ->setTranslatable(TRUE)
      ->setRevisionable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);
    // 签订人
    $fields['person'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Person', [], ['context' => 'Contract']))
      ->setSetting('target_type', 'person')
      ->setRequired(TRUE)
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

    // 合同正文
    // 子合同即关联合同
    // 结算情况
    // 订单
    // 相关流程
    // 提醒计划
    // 附件
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

    $fields['status']->setDescription(t('A boolean indicating whether the Contract is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 签约乙方
    $fields['party_b'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Party B', [], ['context' => 'Contract']))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // 关联项目, dsi_contract.module
    // 关联项目, dsi_project.module

    // 合同期限
    $fields['period'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Period', [], ['context' => 'Contract']))
      ->setSetting('unsigned', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // 关联案件, dsi_cases.module

    // 合同状态
    $fields['contract_state'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Contract State'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['contract_state' => 'contract_state'],
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

    // 自动延期
    $fields['automatic_delay'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Automatic Delay', [], ['context' => 'Contract']))
      ->setSetting('unsigned', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'number',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    // 合作内容
    $fields['cooperation_content'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Cooperation Content', [], ['context' => 'Contract']))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  /**
   * {@inheritDoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    $person = \Drupal::service('person.manager')->currentPerson();
    if (!empty($person) && $business_group = $person->getOrganizationByClassification('business_group')) {
      $this->set('business_group', $business_group);
    }

    parent::preSave($storage);
  }

  /**
   * {@inheritDoc}
   */
  public function postSave(EntityStorageInterface $storage, $update = TRUE) {
    parent::postSave($storage, $update);

    if ($this->get('client')->target_id) {
      $this->get('client')->entity->get('contract')->appendItem($this);
      $this->get('client')->entity->save();
    }
  }

}
