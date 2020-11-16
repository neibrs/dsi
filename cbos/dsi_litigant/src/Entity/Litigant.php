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

    $fields['entity_type'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Type',[], ['context' => 'Litigant']))
      ->setRequired(TRUE)
      ->setSettings([
        'allowed_values' => [
          'individual' => t('Individual'),
          'organization' => t('Organization'),
        ],
      ])
      // Set the default value of this field to 'user'.
      ->setDefaultValue('organization')
      ->setDisplayOptions('view', [
        'label' => 'inline',
        'type' => 'string',
        'weight' => -2,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'weight' => -2,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['entity_id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Entity ID'))
      ->setRequired(TRUE)
      ->setDefaultValue(0);

    // 委托方
    $fields['mandating'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Mandating'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'settings' => [
          'display_label' => TRUE,
        ],
        'weight' => 16,
      ])
      ->setDisplayConfigurable('form', TRUE);

    // 属性: 进攻方，防御方，其他参与人(lookup)
    $fields['extra_attribute'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Extra attribute', [], ['context' => 'Litigant']))
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
