<?php

namespace Drupal\person\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Person address entity.
 *
 * @ingroup person
 *
 * @ContentEntityType(
 *   id = "person_address",
 *   label = @Translation("Person address"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\person\PersonAddressListBuilder",
 *     "views_data" = "Drupal\person\Entity\PersonAddressViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\person\Form\PersonAddressForm",
 *       "add" = "Drupal\person\Form\PersonAddressForm",
 *       "edit" = "Drupal\person\Form\PersonAddressForm",
 *       "delete" = "Drupal\person\Form\PersonAddressDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\person\PersonAddressHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\person\PersonInformationAccessControlHandler",
 *   },
 *   base_table = "person_address",
 *   translatable = FALSE,
 *   admin_permission = "administer persons",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/person_address/{person_address}",
 *     "add-form" = "/person_address/add",
 *     "edit-form" = "/person_address/{person_address}/edit",
 *     "delete-form" = "/person_address/{person_address}/delete",
 *     "collection" = "/person_address",
 *   },
 *   field_ui_base_route = "person_address.settings"
 * )
 */
class PersonAddress extends PersonalInformationBase implements PersonAddressInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['type']
      ->setLabel(t('Address type'))
      ->setSetting('handler_settings', [
        'target_bundles' => ['person_address_type' => 'person_address_type'],
        'auto_create' => TRUE,
      ]);

    // TODO: As Of Date
    // TODO: Status

    $fields['name']
      ->setLabel(t('Address'));

    $fields['primary']
      ->setLabel(t('Primary', [], ['context' => 'Default']));

    return $fields;
  }

}
