<?php

namespace Drupal\lookup\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;

/**
 * Defines the Lookup entity.
 *
 * @ingroup lookup
 *
 * @ContentEntityType(
 *   id = "lookup",
 *   label = @Translation("Lookup", context = "Codes"),
 *   label_collection = @Translation("Lookups", context = "Codes"),
 *   bundle_label = @Translation("Lookup type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\lookup\LookupListBuilder",
 *     "views_data" = "Drupal\lookup\Entity\LookupViewsData",
 *     "translation" = "Drupal\lookup\LookupTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\lookup\Form\LookupForm",
 *       "add" = "Drupal\lookup\Form\LookupForm",
 *       "edit" = "Drupal\lookup\Form\LookupForm",
 *       "delete" = "Drupal\lookup\Form\LookupDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\lookup\LookupHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\lookup\LookupAccessControlHandler",
 *   },
 *   base_table = "lookup",
 *   data_table = "lookup_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer lookups",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *     "master" = "master",
 *   },
 *   links = {
 *     "canonical" = "/lookup/{lookup}",
 *     "add-page" = "/lookup/add",
 *     "add-form" = "/lookup/add/{lookup_type}",
 *     "edit-form" = "/lookup/{lookup}/edit",
 *     "delete-form" = "/lookup/{lookup}/delete",
 *     "collection" = "/lookup",
 *   },
 *   bundle_entity_type = "lookup_type",
 *   field_ui_base_route = "entity.lookup_type.edit_form",
 *   effective_dates_entity = TRUE,
 * )
 */
class Lookup extends ContentEntityBase implements LookupInterface {

  use EntityChangedTrait;
  use EntityPublishedTrait;

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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Code'))
      ->setSetting('max_length', 16)
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Meaning'))
      ->setRequired(TRUE)
      ->setSettings([
        'max_length' => 32,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => -20,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setSetting('max_length', 32)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => -10,
        'label' => 'inline',
      ])
      ->setDisplayConfigurable('view', TRUE)
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE);

    $fields['pinyin'] = BaseFieldDefinition::create('pinyin_shortcode')
      ->setLabel(t('Pinyin shortcode'))
      ->setSetting('source_field', 'description');

    $fields['effective_dates'] = BaseFieldDefinition::create('daterange')
      ->setLabel(t('Effective dates'))
      ->setSetting('datetime_type', DateTimeItem::DATETIME_TYPE_DATE)
      ->setSetting('optional_end_date', TRUE)
      ->setDisplayOptions('view', [
        'type' => 'daterange_default',
        'weight' => 10,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'daterange_default',
        'weight' => 10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['status']->setLabel(t('Enabled'))
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'weight' => 10,
        'label' => 'inline',
        'settings' => [
          'format' => 'unicode-yes-no',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => -20,
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

}
