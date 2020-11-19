<?php


namespace Drupal\dsi_finance\Entity;


use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\dsi_finance\Entity\FinanceExpenditureInterface;
use Drupal\user\UserInterface;

/**
 * Defines the FinanceExpenditure entity.
 *
 * @ingroup dsi_finance
 *
 * @ContentEntityType(
 *   id = "dsi_finance_expenditure",
 *   label = @Translation("Finance Expenditure"),
 *   label_collection = @Translation("Finance Expenditure"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_finance\FinanceExpenditureListBuilder",
 *     "views_data" = "Drupal\dsi_finance\Entity\FinanceExpenditureViewsData",
 *     "translation" = "Drupal\dsi_finance\FinanceExpenditureTranslationHandler",
 *     "access" = "Drupal\dsi_finance\FinanceExpenditureAccessControlHandler",
 *     "form" = {
 *       "default" = "Drupal\dsi_finance\Form\FinanceExpenditureForm",
 *       "add" = "Drupal\dsi_finance\Form\FinanceExpenditureForm",
 *       "edit" = "Drupal\dsi_finance\Form\FinanceExpenditureForm",
 *       "delete" = "Drupal\dsi_finance\Form\FinanceExpenditureDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_finance\FinanceExpenditureHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "dsi_finance_expenditure",
 *   data_table = "dsi_finance_expenditure_field_data",
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
 *     "canonical" = "/dsi_finance_expenditure/{dsi_finance_expenditure}",
 *     "add-form" = "/dsi_finance_expenditure/add",
 *     "edit-form" = "/dsi_finance_expenditure/{dsi_finance_expenditure}/edit",
 *     "delete-form" = "/dsi_finance_expenditure/{dsi_finance_expenditure}/delete",
 *     "collection" = "/dsi_finance_expenditure",
 *   },
 *   field_ui_base_route = "dsi_finance_expenditure.settings"
 * )
 */
class FinanceExpenditure extends ContentEntityBase implements FinanceExpenditureInterface{

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

    //支出人
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
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    // TODO, Add fields.


    //金额
    $fields['price'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Price', [], ['context' => 'Finance']))
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


    //约定收款日
    $fields['happen_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Happen  Date', [], ['context' => 'FinanceExpenditure']))
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

    //关联案件
    $fields['cases'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Cases', [], ['context' => 'FinanceExpenditure']))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
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



    //报销状态
    $fields['reimbursement_status'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Reimbursement Status', [], ['context' => 'FinanceExpenditure']));

    //备注
    $fields['remarks'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Remarks', [], ['context' => 'FinanceExpenditure']))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'text_textfield',
        'weight' => 0,
      ]);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}