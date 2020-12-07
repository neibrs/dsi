<?php

namespace Drupal\dsi_client\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\person\Entity\PersonTrait;
use Drupal\user\UserInterface;

/**
 * Defines the Client entity.
 *
 * @ingroup dsi_client
 *
 * @ContentEntityType(
 *   id = "dsi_client",
 *   label = @Translation("Client"),
 *   bundle_label = @Translation("Client type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_client\ClientListBuilder",
 *     "views_data" = "Drupal\dsi_client\Entity\ClientViewsData",
 *     "translation" = "Drupal\dsi_client\ClientTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\dsi_client\Form\ClientForm",
 *       "add" = "Drupal\dsi_client\Form\ClientForm",
 *       "edit" = "Drupal\dsi_client\Form\ClientForm",
 *       "delete" = "Drupal\dsi_client\Form\ClientDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_client\ClientHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\dsi_client\ClientAccessControlHandler",
 *   },
 *   base_table = "dsi_client",
 *   data_table = "dsi_client_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer client entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/dsi_client/{dsi_client}",
 *     "add-page" = "/dsi_client/add",
 *     "add-form" = "/dsi_client/add/{dsi_client_type}",
 *     "edit-form" = "/dsi_client/{dsi_client}/edit",
 *     "delete-form" = "/dsi_client/{dsi_client}/delete",
 *     "collection" = "/dsi_client",
 *   },
 *   bundle_entity_type = "dsi_client_type",
 *   field_ui_base_route = "entity.dsi_client_type.edit_form",
 *   multiple_organization_field = "follow",
 *   personal_owner = "follow",
 * )
 */
class Client extends ContentEntityBase implements ClientInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;
  use PersonTrait;

  /**
   * {@inheritdoc}
   */
  public static function preCreate(EntityStorageInterface $storage_controller, array &$values) {
    parent::preCreate($storage_controller, $values);
    $values += [
      'user_id' => \Drupal::currentUser()->id(),
      'follow' => static::getCurrentPersonId(),
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

    $fields['type']
      ->setLabel(t('Client type', [], ['context' => 'Client']))
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

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Authored by'))
      ->setDescription(t('The user ID of author of the Client entity.'))
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
      ->setLabel(t('Name', [], ['context' => 'Client name']))
      ->setDescription(t('The name of the Client entity.'))
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

    // TODO, Add fields.

    // 案件类型, 注入字段
    // 跟进人
    $fields['follow'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Follow', [], ['context' => 'Client']))
      ->setSetting('target_type', 'person')
      ->setDefaultValueCallback(static::getCurrentPersonId())
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
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

    // 客户简述
    $fields['summary'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Summary', [], ['context' => 'Client']))
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

    // 工作摘要/客户介绍: TODO, 摘要信息不出来
    $fields['description'] = BaseFieldDefinition::create('text_with_summary')
      ->setLabel(t('Description', [], ['context' => 'Client']))
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'hidden',
        'type' => 'text_default',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'text_textarea_with_summary',
      ])
      ->setDisplayConfigurable('form', TRUE);

    // 合同使用另外一个实体
    // 合作状态
    $fields['cooperating_state'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Cooperating State', [], ['context' => 'Client']))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['cooperating_state' => 'cooperating_state'],
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

    // 客户编号
    $fields['number'] = BaseFieldDefinition::create('code')
      ->setLabel(t('Number', [], ['context' => 'Client']))
      ->setSetting('max_length', 32)
      ->setSetting('encoding_rules', \Drupal::config('dsi_client.settings')->get('encoding_rules'))
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

    // 客户来源
    $fields['customer_source'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Customer Source', [], ['context' => 'Client']))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['customer_source' => 'customer_source'],
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

    // 客户重要性
    $fields['client_importance'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Client Importance', [], ['context' => 'Client']))
      ->setSetting('target_type', 'lookup')
      ->setSetting('handler_settings', [
        'target_bundles' => ['client_importance' => 'client_importance'],
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

    // TODO,客户标识, 应该存实体ID,现在存的是bundle ID.
    $fields['entity_type'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Entity type', [], ['context' => 'Client']))
      ->setRequired(TRUE)
      ->setSetting('is_ascii', TRUE)
      ->setSetting('max_length', EntityTypeInterface::ID_MAX_LENGTH);

    $fields['entity_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Entity ID'))
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'settings' => [
          'label' => 'hidden',
        ],
      ])
      ->setDefaultValue(0);

    // 所属行业
    $fields['sector'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Sector', [], ['context' => 'Client']))
      ->setSetting('target_type', 'taxonomy_term')
      ->setSetting('handler_settings', [
        'target_bundles' => [
          'sector' => 'sector',
        ],
        'sort' => [
          'field' => '_none',
        ],
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
  /**
   * {@inheritdoc}
   */
  public static function bundleFieldDefinitions(EntityTypeInterface $entity_type, $bundle, array $base_field_definitions) {
    if ($client_type = ClientType::load($bundle)) {
      $fields['entity_id'] = clone $base_field_definitions['entity_id'];
      $fields['entity_id']->setSetting('target_type', $client_type->getTargetEntityTypeId());
      $fields['entity_id']->setDisplayOptions('view', [
        'type' => 'entity_reference_entity_view',
        'weight' => 0,
        'settings' => [
          'view_mode' => 'normal',
          'label' => 'hidden',
        ],
      ]);
      return $fields;
    }
    return [];
  }
}
