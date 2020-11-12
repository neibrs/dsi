<?php

namespace Drupal\person\Entity;

use Drupal\Core\Entity\EntityTypeInterface;

/**
 * Defines the Person email entity.
 *
 * @ingroup person
 *
 * @ContentEntityType(
 *   id = "person_email",
 *   label = @Translation("Person email"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\person\PersonEmailListBuilder",
 *     "views_data" = "Drupal\person\Entity\PersonEmailViewsData",
 *
 *     "form" = {
 *       "default" = "Drupal\person\Form\PersonEmailForm",
 *       "add" = "Drupal\person\Form\PersonEmailForm",
 *       "edit" = "Drupal\person\Form\PersonEmailForm",
 *       "delete" = "Drupal\person\Form\PersonEmailDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\person\PersonEmailHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\person\PersonInformationAccessControlHandler",
 *   },
 *   base_table = "person_email",
 *   translatable = FALSE,
 *   admin_permission = "administer persons",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/person_email/{person_email}",
 *     "add-form" = "/person_email/add",
 *     "edit-form" = "/person_email/{person_email}/edit",
 *     "delete-form" = "/person_email/{person_email}/delete",
 *     "collection" = "/person_email",
 *   },
 *   field_ui_base_route = "person_email.settings"
 * )
 */
class PersonEmail extends PersonalInformationBase implements PersonEmailInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['type']
      ->setLabel(t('Email type'))
      ->setSetting('handler_settings', [
        'target_bundles' => ['person_email_type' => 'person_email_type'],
        'auto_create' => TRUE,
      ]);

    $fields['name']
      ->setLabel(t('Email address'));

    $fields['primary']
      ->setLabel(t('Preferred'));

    return $fields;
  }

}
