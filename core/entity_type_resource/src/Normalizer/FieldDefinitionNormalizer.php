<?php

namespace Drupal\entity_type_resource\Normalizer;

use Drupal\Core\Field\FieldDefinitionInterface;

class FieldDefinitionNormalizer extends DataDefinitionNormalizer {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var array
   */
  protected $supportedInterfaceOrClass = [FieldDefinitionInterface::class];

  /**
   * {@inheritdoc}
   */
  public function normalize($field_definition, $format = NULL, array $context = []) {
    /** @var \Drupal\Core\Field\FieldDefinitionInterface $field_definition */

    $attributes = parent::normalize($field_definition, $format, $context);

    $attributes['name'] = $field_definition->getName();
    $attributes['type'] = $field_definition->getType();
    $attributes['target_bundle'] = $field_definition->getTargetBundle();
    $attributes['translatable'] = $field_definition->isTranslatable();
    // $attributes['field_storage_definition'] = $this->serializer->normalize($field_definition->getFieldStorageDefinition());

    return $attributes;
  }

}
