<?php

namespace Drupal\dsi_project\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\user\UserInterface;

/**
 * Defines the Project entity.
 *
 * @ingroup dsi_project
 *
 * @ContentEntityType(
 *   id = "dsi_project",
 *   label = @Translation("Project"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_project\ProjectListBuilder",
 *     "views_data" = "Drupal\dsi_project\Entity\ProjectViewsData",
 *     "translation" = "Drupal\dsi_project\ProjectTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\dsi_project\Form\ProjectForm",
 *       "add" = "Drupal\dsi_project\Form\ProjectForm",
 *       "edit" = "Drupal\dsi_project\Form\ProjectForm",
 *       "delete" = "Drupal\dsi_project\Form\ProjectDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_project\ProjectHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\dsi_project\ProjectAccessControlHandler",
 *   },
 *   base_table = "dsi_project",
 *   data_table = "dsi_project_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer project entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/dsi_project/{dsi_project}",
 *     "add-form" = "/dsi_project/add",
 *     "edit-form" = "/dsi_project/{dsi_project}/edit",
 *     "delete-form" = "/dsi_project/{dsi_project}/delete",
 *     "collection" = "/dsi_project",
 *   },
 *   field_ui_base_route = "dsi_project.settings"
 * )
 */
class Project extends ContentEntityBase implements ProjectInterface {

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
      ->setDescription(t('The user ID of author of the Project entity.'))
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
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Project entity.'))
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
    //    字段规划:
    //    *　项目类型（枚举）
    //    *　关联客户(枚举）
    //      *　开始时间
    //      *　结束时间
    $fields['effective_dates'] = BaseFieldDefinition::create('daterange')
      ->setLabel(t('Effective dates', [], ['context' => 'Project']))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setSetting('optional_start_date', TRUE)
      ->setSetting('optional_end_date', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'daterange_default',
        'weight' => 90,
        'label' => 'inline',
        'settings' => [
          'format_type' => 'html_date',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'daterange_default',
        'weight' => 90,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);
    //      *　主办律师
    //      *　项目经费
    $fields['cost'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Project Cost'))
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
    //      *　项目状态
    //      *　备注
    $fields['description'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Description', [], ['context' => 'Project']))
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
    //      *　项目联系人（多个）

    $fields['status']->setDescription(t('A boolean indicating whether the Project is published.'))
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
