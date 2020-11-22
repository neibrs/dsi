<?php


namespace Drupal\dsi_finance\Entity;


use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\dsi_finance\Entity\FinanceInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Finance entity.
 *
 * @ingroup dsi_finance
 *
 * @ContentEntityType(
 *   id = "dsi_finance_detailed",
 *   label = @Translation("FinanceDetailed"),
 *   label_collection = @Translation("FinanceDetailed"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_finance\FinanceDetailedListBuilder",
 *     "views_data" = "Drupal\dsi_finance\Entity\FinanceViewsData",
 *     "translation" = "Drupal\dsi_finance\FinanceTranslationHandler",
 *     "access" = "Drupal\dsi_finance\FinanceDetailedAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\dsi_finance\Form\FinanceDetailedForm",
 *       "add" = "Drupal\dsi_finance\Form\FinanceDetailedForm",
 *       "edit" = "Drupal\dsi_finance\Form\FinanceDetailedForm",
 *       "delete" = "Drupal\dsi_finance\Form\FinanceDetailedDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_finance\FinanceDetailedHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "dsi_finance_detailed",
 *   data_table = "dsi_finance_detailed_field_data",
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
 *     "canonical" = "/dsi_finance_detailed/{dsi_finance_detailed}",
 *     "add-form" = "/dsi_finance_detailed/add/{finance_id}/{finance_name}",
 *     "edit-form" = "/dsi_finance_detailed/{dsi_finance_detailed}/{finance_id}/edit",
 *     "collection" = "/dsi_finance_detailed",
 *   },
 *   field_ui_base_route = "dsi_finance_detailed.settings"
 * )
 */
class FinanceDetailed extends ContentEntityBase implements FinanceDetailedInterface{

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

    //费用名称
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    // TODO, Add fields.
    //财务id
    $fields['finance_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Finance Id'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue(0);

    //收支类型
    $fields['type'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('type'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDefaultValue(0)
      ->setRequired(TRUE);


    //金额
    $fields['price'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Price', [], ['context' => 'FinanceDetailed']))
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    //发生日期
    $fields['happen_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Happen  Date', [], ['context' => 'FinanceDetailed']))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setDisplayOptions('view', [
        'type' => 'datetime_default',
        'weight' => 0,
        'label' => 'inline',
        'settings' => [
          'format_type' => 'html_date',
        ],
      ])
      ->setDisplayConfigurable('view', TRUE);

    //收款日期
    $fields['collection_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Collection Date', [], ['context' => 'FinanceDetailed']))
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

    //开票日期
    $fields['invoice_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Invoice Date', [], ['context' => 'FinanceDetailed']))
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

    //开票金额
    $fields['invoice_price'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Invoice Price', [], ['context' => 'FinanceDetailed']))
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    // 开单编号
    $fields['invoice_code'] = BaseFieldDefinition::create('code')
      ->setLabel(t('Invoice Code', [], ['context' => 'FinanceDetailed']))
      ->setSetting('max_length', 32)
      ->setSetting('encoding_rules', \Drupal::config('dsi_finance.settings')->get('encoding_rules'))
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

    //发生人
    $fields['happen_by'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Happen By', [], ['context' => 'FinanceDetailed']))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    //关联案件
    $fields['cases'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Cases', [], ['context' => 'FinanceDetailed']))
      ->setSetting('target_type', 'dsi_cases')
      ->setSetting('handler', 'default')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'author',
        'weight' => 0,
      ])
//      ->setDisplayOptions('form', [
//        'type' => 'options_select',
//        'weight' => 5,
//      ])
//      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);


    //明细状态 1正常 2删除
    $fields['detailed_status'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Detail Status', [], ['context' => 'FinanceDetailed']))
      ->setDefaultValue(1);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}