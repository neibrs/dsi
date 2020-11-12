<?php

use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

/**
 * Post update from 19.06
 */
function person_post_update_190700(&$sandbox) {
  $entity_definition_update_manager = \Drupal::entityDefinitionUpdateManager();
  /** @var \Drupal\Core\Entity\EntityLastInstalledSchemaRepositoryInterface $last_installed_schema_repository */
  $last_installed_schema_repository = \Drupal::service('entity.last_installed_schema.repository');

  $entity_type = $entity_definition_update_manager->getEntityType('person');
  $field_storage_definitions = $last_installed_schema_repository->getLastInstalledFieldStorageDefinitions('person');

  // Set type to code.
  $field_storage_definitions['number'] = BaseFieldDefinition::create('code')
    ->setName('number')
    ->setTargetEntityTypeId('person')
    ->setTargetBundle(NULL)
    ->setLabel(t('Number', [], ['context' => 'Person']))
    ->setSetting('max_length', 32)
    ->setSetting('encoding_rules', \Drupal::config('person.settings')->get('encoding_rules'));

  // Set cardinality to unlimited.
  $field_storage_definitions['picture'] = BaseFieldDefinition::create('image')
    ->setName('picture')
    ->setTargetEntityTypeId('person')
    ->setTargetBundle(NULL)
    ->setLabel(t('Pictures'))
    ->setCardinality(FieldStorageDefinitionInterface::CARDINALITY_UNLIMITED);

  $entity_definition_update_manager->updateFieldableEntityType($entity_type, $field_storage_definitions, $sandbox);
}

/**
 * Convert nationality field to string type.
 */
function person_post_update_nationality(&$sandbox) {
  $entity_definition_update_manager = \Drupal::entityDefinitionUpdateManager();
  /** @var \Drupal\Core\Entity\EntityLastInstalledSchemaRepositoryInterface $last_installed_schema_repository */
  $last_installed_schema_repository = \Drupal::service('entity.last_installed_schema.repository');

  $entity_type = $entity_definition_update_manager->getEntityType('person');
  $field_storage_definitions = $last_installed_schema_repository->getLastInstalledFieldStorageDefinitions('person');

  $field_storage_definitions['nationality'] = BaseFieldDefinition::create('string')
    ->setName('nationality')
    ->setTargetEntityTypeId('person')
    ->setTargetBundle(NULL)
    ->setLabel(t('Nationality'))
    ->setSetting('max_length', 16);

  $entity_definition_update_manager->updateFieldableEntityType($entity_type, $field_storage_definitions, $sandbox);

  return t('Nationality field has been converted to string type.');
}
