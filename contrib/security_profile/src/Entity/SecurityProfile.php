<?php

namespace Drupal\security_profile\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntity;

/**
 * Defines the Security profile entity.
 *
 * @ingroup security_profile
 *
 * @ContentEntityType(
 *   id = "security_profile",
 *   label = @Translation("Security profile"),
 *   label_collection = @Translation("Security profiles"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\security_profile\SecurityProfileListBuilder",
 *     "views_data" = "Drupal\security_profile\Entity\SecurityProfileViewsData",
 *     "translation" = "Drupal\security_profile\SecurityProfileTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\security_profile\Form\SecurityProfileForm",
 *       "add" = "Drupal\security_profile\Form\SecurityProfileForm",
 *       "edit" = "Drupal\security_profile\Form\SecurityProfileForm",
 *       "delete" = "Drupal\security_profile\Form\SecurityProfileDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\security_profile\SecurityProfileHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\security_profile\SecurityProfileAccessControlHandler",
 *   },
 *   base_table = "security_profile",
 *   data_table = "security_profile_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer security profiles",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/security_profile/{security_profile}",
 *     "add-form" = "/security_profile/add",
 *     "edit-form" = "/security_profile/{security_profile}/edit",
 *     "delete-form" = "/security_profile/{security_profile}/delete",
 *     "collection" = "/security_profile",
 *   },
 *   field_ui_base_route = "security_profile.settings"
 * )
 */
class SecurityProfile extends EffectiveDatesBusinessGroupEntity implements SecurityProfileInterface {

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
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Add the published field.
    $fields += static::publishedBaseFieldDefinitions($entity_type);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Name'))
      ->setSettings([
        'max_length' => 32,
        'text_processing' => 0,
      ])
      ->setRequired(TRUE)
      ->setDefaultValue('')
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'string',
        'weight' => -10,
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // Organization security

    $fields['organization_hierarchy'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Organization hierarchy'))
      ->setSetting('target_type', 'organization_hierarchy')
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

    $fields['top_organization'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Top organization'))
      ->setSetting('target_type', 'organization')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['include_top_organization'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Include top organization'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'weight' => 0,
        'label' => 'inline',
        'settings' => [
          'format' => 'unicode-yes-no',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['exclude_business_group'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Exclude business group'))
      ->setDefaultValue(TRUE)
      ->setDisplayOptions('view', [
        'type' => 'boolean',
        'weight' => 0,
        'label' => 'inline',
        'settings' => [
          'format' => 'unicode-yes-no',
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'boolean_checkbox',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
