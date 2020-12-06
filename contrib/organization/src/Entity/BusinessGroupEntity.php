<?php

namespace Drupal\organization\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\entity_plus\Entity\EntityMasterTrait;

abstract class BusinessGroupEntity extends ContentEntityBase implements BusinessGroupEntityInterface {

  use EntityChangedTrait;
  use EntityMasterTrait;

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

    if ($entity_type->hasKey('master')) {
      $fields += static::masterBaseFieldDefinitions($entity_type);
    }

    $fields['business_group'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('Business group'))
      ->setRequired(TRUE)
      ->setSetting('target_type', 'organization')
      ->setSetting('handler_settings', [
        'conditions' => [
          'classifications' => 'business_group',
        ],
      ])
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', TRUE);

    // 导入多业务组组织时，业务组的上级会被设置成错误的数据
    if (\Drupal::moduleHandler()->moduleExists('person')) {
      $fields['business_group']->setDefaultValueCallback(static::class . '::getCurrentBusinessGroupId');
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
  public static function getCurrentBusinessGroupId() {
    if ($business_group = \Drupal::service('person.manager')->currentPersonOrganizationByClassification('business_group')) {
      return $business_group->id();
    }
  }

}
