<?php

namespace Drupal\dsi_ipa\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EditorialContentEntityBase;
use Drupal\Core\Entity\RevisionableInterface;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\user\UserInterface;

/**
 * Defines the IP Address.
 *
 * @ingroup dsi_ipa
 *
 * @ContentEntityType(
 *   id = "dsi_ipa",
 *   label = @Translation("IP Address"),
 *   label_collection = @Translation("IP Address"),
 *   handlers = {
 *     "storage" = "Drupal\dsi_ipa\IpaStorage",
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_ipa\IpaListBuilder",
 *     "views_data" = "Drupal\dsi_ipa\Entity\IpaViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\dsi_ipa\Form\IpaForm",
 *       "add" = "Drupal\dsi_ipa\Form\IpaForm",
 *       "edit" = "Drupal\dsi_ipa\Form\IpaForm",
 *       "delete" = "Drupal\dsi_ipa\Form\IpaDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_ipa\IpaHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\dsi_ipa\IpaAccessControlHandler",
 *   },
 *   base_table = "dsi_ipa",
 *   revision_table = "dsi_ipa_revision",
 *   translatable = FALSE,
 *   admin_permission = "administer ip address",
 *   entity_keys = {
 *     "id" = "id",
 *     "revision" = "vid",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   revision_metadata_keys = {
 *     "revision_user" = "revision_uid",
 *     "revision_created" = "revision_timestamp",
 *     "revision_log_message" = "revision_log"
 *   },
 *   links = {
 *     "canonical" = "/admin/dsi_ipa/{dsi_ipa}",
 *     "add-form" = "/admin/dsi_ipa/add",
 *     "edit-form" = "/admin/dsi_ipa/{dsi_ipa}/edit",
 *     "delete-form" = "/admin/dsi_ipa/{dsi_ipa}/delete",
 *     "version-history" = "/admin/dsi_ipa/{dsi_ipa}/revisions",
 *     "revision" = "/admin/dsi_ipa/{dsi_ipa}/revisions/{dsi_ipa_revision}/view",
 *     "revision_revert" = "/admin/dsi_ipa/{dsi_ipa}/revisions/{dsi_ipa_revision}/revert",
 *     "revision_delete" = "/admin/dsi_ipa/{dsi_ipa}/revisions/{dsi_ipa_revision}/delete",
 *     "collection" = "/admin/dsi_ipa",
 *   },
 *   field_ui_base_route = "dsi_ipa.settings"
 * )
 */
class Ipa extends EditorialContentEntityBase implements IpaInterface {

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
  protected function urlRouteParameters($rel) {
    $uri_route_parameters = parent::urlRouteParameters($rel);

    if ($rel === 'revision_revert' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }
    elseif ($rel === 'revision_delete' && $this instanceof RevisionableInterface) {
      $uri_route_parameters[$this->getEntityTypeId() . '_revision'] = $this->getRevisionId();
    }

    return $uri_route_parameters;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    // If no revision author has been set explicitly,
    // make the dsi_ipa owner the revision author.
    if (!$this->getRevisionUser()) {
      $this->setRevisionUserId($this->getOwnerId());
    }

    // Set the ip number.
    $ip_name = $this->get('name')->value;
    if (ip2long($ip_name)) {
      $this->set('number', ip2long($ip_name));
    }
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
      ->setDescription(t('The user ID of author of the IP Address.'))
      ->setRevisionable(TRUE)
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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the IP Address.'))
      ->setRevisionable(TRUE)
      ->addConstraint('UniqueField')
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'hidden',
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

    // IP Number
    $fields['number'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Number'))
      ->setDescription(t('The number of the IP Address.'))
      ->setRevisionable(TRUE);

    $fields['ip_type'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('Type'))
      ->setSetting('unsigned', TRUE)
      ->setSetting('allowed_values', [
        '1' => t('Static'),
        '2' => t('DHCP'),
        '3' => t('Reserved'),
      ])
      ->setDefaultValue(1)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -2,
      ])
      ->setDisplayOptions('view', [
        'type' => 'list_default',
        'label' => 'inline',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['available'] = BaseFieldDefinition::create('list_integer')
      ->setLabel(t('Available'))
      ->setSetting('unsigned', TRUE)
      ->setSetting('allowed_values', [
        '0' => t('Available'),
        '1' => t('Not available'),
      ])
      ->setDefaultValue(0)
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -2,
      ])
      ->setDisplayOptions('view', [
        'type' => 'list_default',
        'label' => 'inline',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['ip_version'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('IPv4', [], ['context' => 'IPv4, v6']))
      ->setDescription(t('IPv4 or IPv6. Default to IPv4.'))
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'boolean',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE)
      ->setDefaultValue(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the IP Address is published.'))
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -3,
      ]);

    $fields['tags'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Tags'))
      ->setDescription(t('A comma-separated list of ip address tags.'))
      ->setSetting('target_type', 'taxonomy_term')
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('handler_settings', [
        'target_bundles' => [
          'ipa_tags' => 'ipa_tags',
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
        'type' => 'entity_reference_autocomplete_tags',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

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
    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

}
