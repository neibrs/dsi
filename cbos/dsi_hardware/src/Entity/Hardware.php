<?php

namespace Drupal\dsi_hardware\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;

/**
 * Defines the Hardware entity.
 *
 * @ingroup dsi_hardware
 *
 * @ContentEntityType(
 *   id = "dsi_hardware",
 *   label = @Translation("Hardware"),
 *   label_collection = @Translation("Hardware"),
 *   bundle_label = @Translation("Hardware type"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_hardware\HardwareListBuilder",
 *     "views_data" = "Drupal\dsi_hardware\Entity\HardwareViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\dsi_hardware\Form\HardwareForm",
 *       "add" = "Drupal\dsi_hardware\Form\HardwareForm",
 *       "edit" = "Drupal\dsi_hardware\Form\HardwareForm",
 *       "delete" = "Drupal\dsi_hardware\Form\HardwareDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_hardware\HardwareHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\dsi_hardware\HardwareAccessControlHandler",
 *   },
 *   base_table = "dsi_hardware",
 *   translatable = FALSE,
 *   admin_permission = "administer hardware entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/dsi_hardware/{dsi_hardware}",
 *     "add-page" = "/dsi_hardware/add",
 *     "add-form" = "/dsi_hardware/add/{dsi_hardware_type}",
 *     "edit-form" = "/dsi_hardware/{dsi_hardware}/edit",
 *     "delete-form" = "/dsi_hardware/{dsi_hardware}/delete",
 *     "collection" = "/dsi_hardware",
 *   },
 *   bundle_entity_type = "dsi_hardware_type",
 *   field_ui_base_route = "entity.dsi_hardware_type.edit_form"
 * )
 */
class Hardware extends ContentEntityBase implements HardwareInterface {

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

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setDescription(t('The name of the Hardware entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['part'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Part #'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
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

    $fields['watts'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Watts'))
      ->setDescription(t('Watts per PSU'))
      ->setSettings([
        'max_length' => 10,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
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

    $fields['add_port'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Add Ports When Creating Device'))
      ->setDescription(t('Check if you want ports to be automatically created when a device is created from UI or API.'))
      ->setDefaultValue(FALSE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'weight' => 0,
        'settings' => [
          'format' => 'unicode-yes-no',
        ],
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['specification'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Specification URL:'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
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

    // TODO, front_picture, back_picture 单独管理，比如设备的图片管理
    $fields['front_picture'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Front Pictures'))
      ->setDisplayOptions('view', [
        'type' => 'image',
        'weight' => 100,
        'label' => 'inline',
        'settings' => [
          'image_style' => '80x',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 100,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['back_picture'] = BaseFieldDefinition::create('image')
      ->setLabel(t('Back Pictures'))
      ->setDisplayOptions('view', [
        'type' => 'image',
        'weight' => 100,
        'label' => 'inline',
        'settings' => [
          'image_style' => '80x',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'image_image',
        'weight' => 100,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['end_life_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('End of Life Date'))
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

    $fields['end_support_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('End of U Date'))
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

    $fields['notes'] = BaseFieldDefinition::create('string')
      ->setLabel('Notes')
      ->setSettings([
        'max_length' => 0,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
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

    // TODO, move to provider.
    //    $fields['provider'] = BaseFieldDefinition::create('entity_reference')
    //      ->setLabel(t('Vendor'))
    //      ->setSetting('target_type', 'dsi_provider')
    //      ->setDisplayOptions('view', [
    //        'type' => 'entity_reference_label',
    //        'weight' => 0,
    //        'label' => 'inline',
    //      ])
    //      ->setDisplayOptions('form', [
    //        'type' => 'entity_reference_autocomplete',
    //        'weight' => 0,
    //      ])
    //      ->setDisplayConfigurable('form', TRUE)
    //      ->setDisplayConfigurable('view', TRUE);

    // TODO Add fields, port, part slots, hardware alias, custom fields, device
    $fields['status']->setDescription(t('A boolean indicating whether the Hardware is published.'))
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
