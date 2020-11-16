<?php

namespace Drupal\dsi_cases\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\user\UserInterface;

/**
 * Defines the Cases entity.
 *
 * @ingroup dsi_cases
 *
 * @ContentEntityType(
 *   id = "dsi_cases",
 *   label = @Translation("Cases"),
 *   label_collection = @Translation("Cases"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_cases\CasesListBuilder",
 *     "views_data" = "Drupal\dsi_cases\Entity\CasesViewsData",
 *     "translation" = "Drupal\dsi_cases\CasesTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\dsi_cases\Form\CasesForm",
 *       "add" = "Drupal\dsi_cases\Form\CasesForm",
 *       "edit" = "Drupal\dsi_cases\Form\CasesForm",
 *       "delete" = "Drupal\dsi_cases\Form\CasesDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_cases\CasesHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\dsi_cases\CasesAccessControlHandler",
 *   },
 *   base_table = "dsi_cases",
 *   data_table = "dsi_cases_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer cases entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/dsi_cases/{dsi_cases}",
 *     "add-form" = "/dsi_cases/add",
 *     "edit-form" = "/dsi_cases/{dsi_cases}/edit",
 *     "delete-form" = "/dsi_cases/{dsi_cases}/delete",
 *     "collection" = "/dsi_cases",
 *   },
 *   field_ui_base_route = "dsi_cases.settings"
 * )
 */
class Cases extends ContentEntityBase implements CasesInterface {

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
      ->setDescription(t('The user ID of author of the Cases entity.'))
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

    //    * 案件名称(name)
    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name', [], ['context' => 'Case']))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
        'label' => 'inline',
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
    //    * 案件类型
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

    //    * 案件程序
    $fields['case_procedure'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Case Procedure'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['case_procedure' => 'case_procedure'],
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

    //    * 案由
    $fields['case_cause'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Case Cause'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['case_cause' => 'case_cause'],
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


    // 案件简略登记，案件来源，联系人电话
    // 案件来源
    $fields['case_source'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Case Source'))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['case_source' => 'case_source'],
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

    // 联系人电话
    $fields['contact_phone'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Contact Phone', [], ['context' => 'Cases']))
      ->setSetting('max_length', 255)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'string',
        'weight' => -5,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -5,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayConfigurable('form', TRUE);

    //    * 案号
    $fields['number'] = BaseFieldDefinition::create('code')
      ->setLabel(t('Number', [], ['context' => 'Case']))
      ->setSetting('max_length', 32)
      ->setSetting('encoding_rules', \Drupal::config('dsi_cases.settings')->get('encoding_rules'))
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
    //    * 委托时间
    $fields['delegate_datetime'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Delegate  Datetime', [], ['context' => 'Cases']))
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

    //    * 案件标的(金额)
    $fields['subject_matter'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Subject Matter', [], ['context' => 'Cases']))
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

    //    * 主办律师(Owner?)
    $fields['person_in_charge'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Person in Charge', [], ['context' => 'Cases']))
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

    $fields['status']
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

}
