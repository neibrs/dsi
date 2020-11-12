<?php

namespace Drupal\person\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Person phone entity.
 *
 * @ingroup person
 *
 * @ContentEntityType(
 *   id = "person_phone",
 *   label = @Translation("Person phone"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\person\PersonPhoneListBuilder",
 *     "views_data" = "Drupal\person\Entity\PersonPhoneViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\person\Form\PersonPhoneForm",
 *       "add" = "Drupal\person\Form\PersonPhoneForm",
 *       "edit" = "Drupal\person\Form\PersonPhoneForm",
 *       "delete" = "Drupal\person\Form\PersonPhoneDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\person\PersonPhoneHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\person\PersonInformationAccessControlHandler",
 *   },
 *   base_table = "person_phone",
 *   translatable = FALSE,
 *   admin_permission = "administer persons",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/person_phone/{person_phone}",
 *     "add-form" = "/person_phone/add",
 *     "edit-form" = "/person_phone/{person_phone}/edit",
 *     "delete-form" = "/person_phone/{person_phone}/delete",
 *     "collection" = "/person_phone",
 *   },
 *   field_ui_base_route = "person_phone.settings"
 * )
 */
class PersonPhone extends PersonalInformationBase implements PersonPhoneInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['type']
      ->setLabel(t('Phone type'))
      ->setSetting('handler_settings', [
        'target_bundles' => ['person_phone_type' => 'person_phone_type'],
        'auto_create' => TRUE,
      ]);

    $fields['name']
      ->setLabel(t('Telephone'));

    // TODO: Extension

    $fields['primary']
      ->setLabel(t('Preferred'));

    return $fields;
  }
  
}
