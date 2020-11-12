<?php

namespace Drupal\person\Entity;

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\FieldStorageDefinitionInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;

/**
 * Defines the Identification information entity.
 *
 * @ingroup person
 *
 * @ContentEntityType(
 *   id = "identification_information",
 *   label = @Translation("Identification information"),
 *   label_collection = @Translation("Identification information"),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\person\IdentificationInformationListBuilder",
 *     "views_data" = "Drupal\person\Entity\IdentificationInformationViewsData",
 *     "translation" = "Drupal\person\IdentificationInformationTranslationHandler",
 *
 *     "form" = {
 *       "default" = "Drupal\person\Form\IdentificationInformationForm",
 *       "add" = "Drupal\person\Form\IdentificationInformationForm",
 *       "edit" = "Drupal\person\Form\IdentificationInformationForm",
 *       "delete" = "Drupal\person\Form\IdentificationInformationDeleteForm",
 *     },
 *     "route_provider" = {
 *       "html" = "Drupal\person\IdentificationInformationHtmlRouteProvider",
 *     },
 *     "access" = "Drupal\person\PersonInformationAccessControlHandler",
 *   },
 *   base_table = "identification_information",
 *   data_table = "identification_information_field_data",
 *   translatable = TRUE,
 *   admin_permission = "administer identification information entities",
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "name",
 *     "uuid" = "uuid",
 *     "langcode" = "langcode",
 *     "published" = "status",
 *   },
 *   links = {
 *     "canonical" = "/identification_information/{identification_information}",
 *     "add-form" = "/identification_information/add",
 *     "edit-form" = "/identification_information/{identification_information}/edit",
 *     "delete-form" = "/identification_information/{identification_information}/delete",
 *     "collection" = "/identification_information",
 *   },
 *   field_ui_base_route = "identification_information.settings"
 * )
 */
class IdentificationInformation extends PersonalInformationBase implements IdentificationInformationInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields = parent::baseFieldDefinitions($entity_type);

    $fields['name']
      ->setLabel(t('Identify number'));

    // CV02_01_101身份证件类别代码
    $fields['type']
      ->setLabel(t('Identification type'))
      ->setSetting('handler_settings', [
        'target_bundles' => ['identification_information_type' => 'identification_information_type'],
        'auto_create' => TRUE,
      ]);

    $fields['attachments'] = BaseFieldDefinition::create('file')
      ->setLabel(t('Attachments'))
      ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED)
      ->setSetting('file_extensions', 'doc docx xls xlsx jpeg png txt')
      ->setDisplayOptions('view', [
        'type' => 'file_default',
        'weight' => 110,
        'label' => 'inline',
      ])
      ->setDisplayOptions('form', [
        'type' => 'file_generic',
        'weight' => 110,
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    return $fields;
  }

}
