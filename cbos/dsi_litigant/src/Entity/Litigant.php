<?php

namespace Drupal\dsi_litigant\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Litigant entity.
 *
 * @ingroup dsi_litigant
 *
 * @ContentEntityType(
 *   id = "dsi_litigant",
 *   label = @Translation("Litigant"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\dsi_litigant\LitigantListBuilder",
 *     "views_data" = "Drupal\dsi_litigant\Entity\LitigantViewsData",
 *     "translation" = "Drupal\dsi_litigant\LitigantTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\dsi_litigant\Form\LitigantForm",
 *       "add" = "Drupal\dsi_litigant\Form\LitigantForm",
 *       "edit" = "Drupal\dsi_litigant\Form\LitigantForm",
 *       "delete" = "Drupal\dsi_litigant\Form\LitigantDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\dsi_litigant\LitigantHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\dsi_litigant\LitigantAccessControlHandler",
 *   },
 *   base_table = "dsi_litigant",
 *   data_table = "dsi_litigant_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer litigant entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/dsi_litigant/{dsi_litigant}",
 *     "add-form" = "/dsi_litigant/add",
 *     "edit-form" = "/dsi_litigant/{dsi_litigant}/edit",
 *     "delete-form" = "/dsi_litigant/{dsi_litigant}/delete",
 *     "collection" = "/dsi_litigant",
 *   },
 *   field_ui_base_route = "dsi_litigant.settings"
 * )
 */
class Litigant extends ContentEntityBase implements LitigantInterface {

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
      ->setDescription(t('The name of the Litigant entity.'))
      ->setSettings([
        'max_length' => 50,
        'text_processing' => 0,
      ])
      ->setDefaultValue('')
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
      ->setDisplayConfigurable('view', TRUE)
      ->setRequired(TRUE);

    $fields['status']->setDescription(t('A boolean indicating whether the Litigant is published.'))
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
