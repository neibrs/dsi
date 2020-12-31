<?php

namespace Drupal\dsi_attachment\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Field\FieldStorageDefinitionInterface;

trait AttachmentTrait {

  /**
   * Generate attachments for global.
   */
  public static function attachmentBaseFieldDefinitions(EntityTypeInterface $entity_type) {
    if (!$entity_type->hasKey('attachments')) {
      throw new UnsupportedEntityTypeDefinitionException('The entity type ' . $entity_type->id() . ' does not have a "master" entity key.');
    }

    return [
      $entity_type->getKey('attachments') => BaseFieldDefinition::create('file')
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
        ->setDisplayConfigurable('view', TRUE)
    ];
  }

  /**
   * 实体的附件内容同步反向写到dsi_attachment里面,便于统一管理附件.
   */
  public function setAttachmentByEntity() {
    $attachment_storage = $this->entityTypeManager()->getStorage('dsi_attachment');
    $attachments = $attachment_storage->loadByProperties([
      'entity_type' => $this->getEntityTypeId(),
      'entity_id' => $this->id(),
    ]);
    $attachment = reset($attachments);
    if (empty($attachment)) {
      $attachment = $attachment_storage->create([
        'entity_type' => $this->getEntityTypeId(),
        'entity_id' => $this->id()
      ]);
    }
    $attachment->set('attachments', $this->get('attachments')->referencedEntities());
    $attachment->save();
  }

}
