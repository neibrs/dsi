<?php

namespace Drupal\layout_template\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\user\UserInterface;

/**
 * Defines the Layout template entity.
 *
 * @ingroup layout_template
 *
 * @ContentEntityType(
 *   id = "layout_template",
 *   label = @Translation("Layout template"),
 *   bundle_label = @Translation("Layout template type"),
 *   handlers = {
 *     "list_builder" = "Drupal\layout_template\LayoutTemplateListBuilder",
 *     "views_data" = "Drupal\layout_template\Entity\LayoutTemplateViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\layout_template\Form\LayoutTemplateForm",
 *       "add" = "Drupal\layout_template\Form\LayoutTemplateForm",
 *       "edit" = "Drupal\layout_template\Form\LayoutTemplateForm",
 *       "delete" = "Drupal\layout_template\Form\LayoutTemplateDeleteForm",
 *     },
 *     "access" = "Drupal\layout_template\LayoutTemplateAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\layout_template\LayoutTemplateHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "layout_template",
 *   admin_permission = "administer layout template entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "uid" = "user_id",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "add-page" = "/layout_template/add",
 *     "edit-form" = "/layout_template/{layout_template}/edit",
 *     "delete-form" = "/layout_template/{layout_template}/delete",
 *     "collection" = "/layout_template",
 *   },
 *   bundle_entity_type = "layout_template_type",
 *   field_ui_base_route = "entity.layout_template_type.edit_form"
 * )
 */
class LayoutTemplate extends ContentEntityBase implements LayoutTemplateInterface {

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
      ->setDescription(t('The user ID of author of the Layout template entity.'))
      ->setRevisionable(TRUE)
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default')
      ->setTranslatable(TRUE)
      ->setDisplayOptions('view', [
        'type' => 'author',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Layout template name'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setRequired(TRUE)
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -4,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['related_config'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Config ID'))
      // The default target_type setting is 'node', witch target_id type is int.
      // Set target_type to entity_form_display will force target_id type into string.
      ->setSetting('target_type', 'entity_form_display')
      ->setRequired(TRUE);

    $fields['configuration'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Configuration'));

    $fields['is_public'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Is public'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'weight' => -4,
      ])
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -4,
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
   * {@inheritdoc}
   */
  public static function bundleFieldDefinitions(EntityTypeInterface $entity_type, $bundle, array $base_field_definitions) {
    if ($type = LayoutTemplateType::load($bundle)) {
      $fields['related_config'] = clone $base_field_definitions['related_config'];
      $fields['related_config']->setSetting('target_type', $bundle);
      return $fields;
    }
    return [];
  }

  public function isPublic() {
    return $this->is_public->value;
  }

  /**
   * {@inheritdoc}
   */
  public function getRelatedConfig() {
    if ($entity = $this->related_config->entity) {
      return $entity;
    }

    list ($entity_type_id, $bundle, $mode) = explode('.', $this->related_config->target_id);
    /** @var \Drupal\layout_template\LayoutTemplateManager $layout_template_manager */
    $layout_template_manager = \Drupal::service('layout_template.manager');
    return $layout_template_manager->getEntityFormDisplay($entity_type_id, $bundle, $mode);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    $value = $this->configuration->getValue();
    return reset($value);
  }

}
