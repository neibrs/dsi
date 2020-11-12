<?php

namespace Drupal\organization\Entity;

use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Defines the Organization entity.
 *
 * @ingroup organization
 *
 * @ContentEntityType(
 *   id = "organization",
 *   label = "组织",
 *   label_collection = @Translation("Organizations"),
 *   bundle_label = @Translation("Organization type"),
 *   handlers = {
 *     "storage" = "Drupal\organization\OrganizationStorage",
 *     "view_builder" = "Drupal\organization\OrganizationViewBuilder",
 *     "list_builder" = "Drupal\organization\OrganizationListBuilder",
 *     "views_data" = "Drupal\organization\Entity\OrganizationViewsData",
 *     "translation" = "Drupal\organization\OrganizationTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\organization\Form\OrganizationForm",
 *       "add" = "Drupal\organization\Form\OrganizationForm",
 *       "edit" = "Drupal\organization\Form\OrganizationForm",
 *       "delete" = "Drupal\organization\Form\OrganizationDeleteForm",
 *       "delete-multiple-confirm" = "Drupal\Core\Entity\Form\DeleteMultipleForm"
 *     },
 *     "access" = "Drupal\organization\OrganizationAccessControlHandler",
 *     "route_provider" = {
 *       "html" = "Drupal\organization\OrganizationHtmlRouteProvider",
 *     },
 *     "inline_form" = "Drupal\organization\Form\OrganizationInlineForm",
 *   },
 *   base_table = "organization",
 *   data_table = "organization_field_data",
 *   translatable = TRUE,
 *   admin_permission = "maintain organizations",
 *   entity_keys = {
 *     "id" = "id",
 *     "bundle" = "type",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/organization/{organization}",
 *     "add-page" = "/organization/add",
 *     "add-form" = "/organization/add/{organization_type}",
 *     "edit-form" = "/organization/{organization}/edit",
 *     "delete-form" = "/organization/{organization}/delete",
 *     "delete-multiple-form" = "/organization/delete",
 *     "collection" = "/organization",
 *     "children" = "/organization/{organization}/children",
 *     "merge-form" = "/organization/merge",
 *   },
 *   bundle_entity_type = "organization_type",
 *   field_ui_base_route = "entity.organization_type.edit_form",
 *   common_reference_target = TRUE,
 *   multiple_organization_classification = "business_group",
 *   effective_dates_entity = TRUE,
 *   match_fields = {
 *     "name",
 *     "description",
 *     "pinyin",
 *   }
 * )
 */
class Organization extends EffectiveDatesBusinessGroupEntity implements OrganizationInterface {

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
  public function getParent() {
    return $this->get('parent')->entity;
  }

  /**
   * {@inheritdoc}
   */
  public function setParent($organizations) {
    foreach($organizations as $organization) {
      $organization->set('parent', $this->id());
    }
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function hasClassification($classification) {
    $values = $this->get('classifications')->getValue();
    $found = array_filter($values, function ($item) use ($classification) {
      return $item['target_id'] == $classification;
    });
    return !empty($found);
  }

  /**
   * {@inheritdoc}
   */
  public function getByClassification($classification_id) {
    if ($this->hasClassification($classification_id)) {
      return $this;
    }

    $organization = $this;
    while ($organization = $organization->getParent()) {
      if ($organization->hasClassification($classification_id)) {
        return $organization;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function loadAllChildren() {
    $children = $this->loadChildren();

    foreach ($children as $child) {
      $children += $child->loadAllChildren();
    }

    return $children;
  }

  /**
   * {@inheritdoc}
   */
  public function loadChildren($status = FALSE) {
    $storage = $this->entityTypeManager()->getStorage('organization');
    $query = $storage->getQuery();
    $query->condition('parent', $this->id());
    if ($status) {
      $query->condition('status', TRUE);
    }
    $ids = $query->execute();

    return $storage->loadMultiple($ids);
  }

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    // Let cloud manager can add top organization.
    $fields['business_group']->setRequired(FALSE);

    $fields['name'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Organization'))
      ->setRequired(TRUE)
      ->setSetting('max_length', 32)
      ->setSetting('encoding_rules', \Drupal::config('organization.settings')->get('encoding_rules'))
      ->addConstraint('MultiOrganizationUniqueField')
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => -40,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -40,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['description'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Description'))
      ->setSetting('max_length', 64)
      ->setDisplayOptions('view', [
        'type' => 'string',
        'weight' => -30,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'string_textfield',
        'weight' => -30,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['pinyin'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Pinyin shortcode'))
      ->setSetting('max_length', 64);

    $fields['type']
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => -20,
        'label' => 'inline',
        'settings' => [
          'link' => FALSE,
        ],
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => -20,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['parent'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Parent organization'))
      ->setSetting('target_type', 'organization')
      ->addConstraint('OrganizationParent')
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => -10,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'entity_reference_autocomplete',
        'weight' => -10,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['location'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Location', [], ['context' => 'Work place']))
      ->setSetting('target_type', 'location')
      ->setSetting('handler_settings', [
        'auto_create' => TRUE,
      ])
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

    $fields['classifications'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Classification'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('target_type', 'organization_classification')
      ->setDefaultValue(['hr_organization'])
      ->setDisplayOptions('view', [
        'type' => 'entity_reference_label',
        'weight' => 0,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_buttons',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    $fields['currency'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Currency'))
      ->setSetting('target_type', 'currency')
      ->setDefaultValueCallback('Drupal\organization\Entity\Organization::getDefaultCurrencyId')
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ])
      ->setDisplayConfigurable('form', TRUE);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    parent::preSave($storage);

    if (!$this->isNew() && $parent = $this->parent->target_id) {
      // 不允许 parent 为本身.
      if ($this->id() == $parent) {
        $this->parent->target_id = NULL;
      }
      
      // 不允许 parent 嵌套循环.
      $children = $this->loadAllChildren();
      if (in_array($this->id(), array_keys($children))) {
        $this->parent->target_id = NULL;
      }
    }
    
    // Change business group.
    if ($parent = $this->getParent()) {
      if ($business_group = $parent->getByClassification('business_group')) {
        $this->business_group->target_id = $business_group->id();
      }
    }

    // Generate pinyin code.
    $label = $this->get('name')->value . $this->get('description')->value;
    $pinyin = \Drupal::service('pinyin.shortcode')->transliterate($label, 'en', '?', 64);
    $this->set('pinyin', $pinyin);
  }

  public static function getDefaultCurrencyId() {
    return \Drupal::config('currency.settings')->get('default_currency');
  }

  /**
   * {@inheritdoc}
   */
  public function label() {
    $items = [];
    $items[] = $this->get('name')->value;
    $description = $this->get('description')->value;
    if (!empty($description)) {
      $items[] = $description;
    }
    return implode('-', $items);
  }

  /**
   * {@inheritdoc}
   */
  public function addClassification($classification_id) {
    $classifications = $this->getClassifications();
    $classifications[] = $classification_id;
    $this->set('classifications', array_unique($classifications));
  }

  /**
   * {@inheritdoc}
   */
  public function getClassifications() {
    $classifications = [];

    foreach ($this->get('classifications') as $classification) {
      $classifications[] = $classification->target_id;
    }

    return $classifications;
  }

}
