<?php


namespace Drupal\dsi_finance\Entity;


use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\dsi_finance\Entity\FinanceInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Finance entity.
 *
 * @ingroup dsi_finance
 *
 * @ContentEntityType(
 *   id = "dsi_finance",
 *   label = @Translation("Finance"),
 *   label_collection = @Translation("Finance"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_finance\FinanceListBuilder",
 *     "views_data" = "Drupal\dsi_finance\Entity\FinanceViewsData",
 *     "translation" = "Drupal\dsi_finance\FinanceTranslationHandler",
 *     "access" = "Drupal\dsi_finance\FinanceAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\dsi_finance\Form\FinanceForm",
 *       "add" = "Drupal\dsi_finance\Form\FinanceForm",
 *       "edit" = "Drupal\dsi_finance\Form\FinanceForm",
 *       "delete" = "Drupal\dsi_finance\Form\FinanceDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_finance\FinanceHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "dsi_finance",
 *   data_table = "dsi_finance_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer finance entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/dsi_finance/{dsi_finance}",
 *     "add-form" = "/dsi_finance/add",
 *     "edit-form" = "/dsi_finance/{dsi_finance}/edit",
 *     "delete-form" = "/dsi_finance/{dsi_finance}/delete",
 *     "collection" = "/dsi_finance",
 *   },
 *   field_ui_base_route = "dsi_finance.settings"
 * )
 */
class Finance extends ContentEntityBase implements FinanceInterface{

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

    //创建人
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
//      ->setDisplayOptions('form', [
//        'type' => 'entity_reference_autocomplete',
//        'weight' => 5,
//        'settings' => [
//          'match_operator' => 'CONTAINS',
//          'size' => '60',
//          'autocomplete_type' => 'tags',
//          'placeholder' => '',
//        ],
//      ])
//      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    //款项名称
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
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

    //应收金额
    $fields['receivable_price'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Receivable Price', [], ['context' => 'Finance']))
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);


    //已收金额
    $fields['received_price'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Received Price', [], ['context' => 'Finance']))
      ->setSetting('size', 'big')
      ->setDisplayOptions('view', [
        'type' => 'number_decimal',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayConfigurable('view', TRUE);

    //待收金额
    $fields['wait_price'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Wait Price', [], ['context' => 'Finance']))
      ->setDisplayOptions('view', [
        'type' => 'number_decimal',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayConfigurable('view', TRUE);

    //约定收款日期
    $fields['appointment_time'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Appointment  Time', [], ['context' => 'Finance']))
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

    //关联类型
    $fields['relation_type'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Relation Type', [], ['context' => 'Finance']))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['relation_type' => 'relation_type'],
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setRequired(TRUE);

    //关联案件 || 项目 || 客户
    $fields['relation'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('', [], ['context' => 'Finance']))
      ->setSetting('target_type', 'dsi_cases')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 5,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['detail'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Detail', [], ['context' => 'Finance detail']))
      ->setSetting( 'target_type', 'dsi_finance_detailed')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setDisplayOptions('form', [
        'type' => 'inline_entity_form_complex',
        'settings' => [
          'form_mode' => 'inline_entity_form',
        ],
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    //备注
    $fields['remarks'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Remarks', [], ['context' => 'Finance']))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'text_textarea',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}