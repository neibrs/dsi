<?php

namespace Drupal\location\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the Location entity.
 *
 * @ingroup location
 *
 * @ContentEntityType(
 *   id = "location",
 *   label = @Translation("Location", context = "Work Structures"),
 *   label_collection = @Translation("Locations", context="Work Structures"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\location\LocationListBuilder",
 *     "views_data" = "Drupal\location\Entity\LocationViewsData",
 *     "translation" = "Drupal\location\LocationTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\location\Form\LocationForm",
 *       "add" = "Drupal\location\Form\LocationForm",
 *       "edit" = "Drupal\location\Form\LocationForm",
 *       "delete" = "Drupal\location\Form\LocationDeleteForm",
 *     },
 *     "access" = "Drupal\location\LocationAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\location\LocationHtmlRouteProvider",
 *     },
 *   },
 *   base_table = "location",
 *   data_table = "location_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer locations",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/location/{location}",
 *     "add-form" = "/location/add",
 *     "edit-form" = "/location/{location}/edit",
 *     "delete-form" = "/location/{location}/delete",
 *     "collection" = "/location",
 *   },
 *   field_ui_base_route = "location.settings"
 * )
 */
class Location extends ContentEntityBase implements LocationInterface {

  use EntityChangedTrait;

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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Location'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 32)
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
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setSetting('max_length', 64)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['address'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Address'))
      ->setTranslatable(TRUE)
      ->setSetting('max_length', 255)
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

    $fields['pinyin'] = BaseFieldDefinition::create('pinyin_shortcode')
      ->setLabel(t('Pinyin shortcode'))
      ->setSetting('source_field', 'address');

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  public function label() {
    $items = [];
    $items[] = $this->get('name')->value;
    $address = $this->get('address')->value;
    if (!empty($address)) {
      $items[] = $address;
    }
    return implode('-', $items);
  }

}
