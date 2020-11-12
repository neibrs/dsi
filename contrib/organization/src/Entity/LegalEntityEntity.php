<?php

namespace Drupal\organization\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\entity_plus\Entity\EntityMasterTrait;

abstract class LegalEntityEntity extends ContentEntityBase implements LegalEntityEntityInterface {

  use EntityChangedTrait;
  use EntityMasterTrait;

  /**
   * {@inheritdoc}
   */
  public function getCreatedTime() {
    return $this->get('created')-value;
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

    if ($entity_type->hasKey('master')) {
      $fields += static::masterBaseFieldDefinitions($entity_type);
    }

   $fields['legal_entity'] = BaseFieldDefinition::create('entity_reference')
     ->setLabel(t('Legal entity'))
     ->setRequired(TRUE)
     ->setSetting('target_type', 'organization')
     ->setSetting('handler_settings', [
       'conditions' => [
         'classifications' => 'legal_entity',
       ],
     ])
     ->setDisplayOptions('view', [
       'type' => 'entity_reference_label',
       'weight' => -100,
       'label' => 'inline',
       'settings' => [
         'link' => FALSE,
       ],
     ])
     ->setDisplayOptions('form', [
       'type' => 'options_select',
       'weight' => -100,
     ])
     ->setDisplayConfigurable('form', TRUE)
     ->setDisplayConfigurable('view', TRUE);

   if (\Drupal::moduleHandler()->moduleExists('person')) {
     $fields['legal_entity']->setDefaultValueCallback(static::class . '::getCurrentLegalEntityId');
   }

   $fields['created'] = BaseFieldDefinition::create('created')
     ->setLabel(t('Created'))
     ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

    return $fields;
  }

  /**
   * This method will only be called after the person module has been enabled.
   *
   * @see ::baseFieldDefinitions()
   */
  public static function getCurrentLegalEntityId() {
    if ($person = \Drupal::service('person.manager')->currentPerson()) {
      if ($organization = $person->getOrganizationByClassification('legal_entity')) {
        return $organization->id();
      }
    }
  }

}
