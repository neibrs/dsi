<?php

namespace Drupal\administrative_area\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Administrative area entity.
 *
 * @ingroup administrative_area
 *
 * @ContentEntityType(
 *   id = "administrative_area",
 *   label = @Translation("Administrative area"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\administrative_area\AdministrativeAreaListBuilder",
 *     "views_data" = "Drupal\administrative_area\Entity\AdministrativeAreaViewsData",
 *     "translation" = "Drupal\administrative_area\AdministrativeAreaTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\administrative_area\Form\AdministrativeAreaForm",
 *       "add" = "Drupal\administrative_area\Form\AdministrativeAreaForm",
 *       "edit" = "Drupal\administrative_area\Form\AdministrativeAreaForm",
 *       "delete" = "Drupal\administrative_area\Form\AdministrativeAreaDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\administrative_area\AdministrativeAreaHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\administrative_area\AdministrativeAreaAccessControlHandler",
 *   },
 *   base_table = "administrative_area",
 *   data_table = "administrative_area_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer administrative areas",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *   },
 *   links = {
 *     "canonical" = "/administrative_area/{administrative_area}",
 *     "add-form" = "/administrative_area/add",
 *     "edit-form" = "/administrative_area/{administrative_area}/edit",
 *     "delete-form" = "/administrative_area/{administrative_area}/delete",
 *     "collection" = "/administrative_area",
 *   },
 *   field_ui_base_route = "administrative_area.settings"
 * )
 */
class AdministrativeArea extends ContentEntityBase implements AdministrativeAreaInterface {

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
      ->setLabel(t('Name'))
      ->setSetting('max_length', 32)
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

    $fields['pinyin'] = BaseFieldDefinition::create('pinyin_shortcode')
      ->setLabel(t('Pinyin shortcode'))
      ->setSetting('source_field', 'name');

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

    $fields['parent'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Parent'))
      ->setSetting('target_type', 'administrative_area')
      ->setDisplayOptions('view', [
        'label' => 'entity_reference_label',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['type'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Type'))
      ->setSetting('allowed_values', [
        'country' => t('Country'),
        'administrative_area' => t('Administrative area'),
        'locality' => t('Locality'),
        'thoroughfare' => t('Thoroughfare'),
      ])
      ->setDisplayOptions('view', [
        'label' => 'list_default',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
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
  public function label() {
    $items = [];
    if (!empty($this->parent->target_id)) {
      $items[] = $this->parent->entity->label();
    }
    if (!empty($this->getName())) {
      $items[] = $this->getName();
    }

    return implode('-', $items);
  }

}
