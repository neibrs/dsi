<?php

namespace Drupal\entity_plus\Entity;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Exception\UnsupportedEntityTypeDefinitionException;
use Drupal\Core\Field\BaseFieldDefinition;

trait EntityMasterTrait {

  public static function masterBaseFieldDefinitions(EntityTypeInterface $entity_type) {
    if (!$entity_type->hasKey('master')) {
      throw new UnsupportedEntityTypeDefinitionException('The entity type ' . $entity_type->id() . ' does not have a "master" entity key.');
    }

    return [
      $entity_type->getKey('master') => BaseFieldDefinition::create('boolean')
        ->setLabel(t('Master', [], ['context' => 'Data']))
        ->setDefaultValue(FALSE)
        ->setDisplayOptions('view', [
          'type' => 'boolean',
          'weight' => -90,
          'label' => 'inline',
          'settings' => [
            'format' => 'yes-no',
          ],
        ])
        ->setDisplayOptions('form', [
          'type' => 'boolean_checkbox',
          'weight' => -90,
        ])
        ->setDisplayConfigurable('form', TRUE)
        ->setDisplayConfigurable('view', TRUE),
    ];
  }

}
