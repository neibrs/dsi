<?php

namespace Drupal\entity_type_resource\Normalizer;

use Drupal\Core\TypedData\DataDefinitionInterface;
use Drupal\serialization\Normalizer\NormalizerBase;

/**
 * Normalizes data definition object into an array structure.
 */
class DataDefinitionNormalizer extends NormalizerBase {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var array
   */
  protected $supportedInterfaceOrClass = [DataDefinitionInterface::class];

  /**
   * {@inheritdoc}
   */
  public function normalize($data_definition, $format = NULL, array $context = []) {
    /** @var \Drupal\Core\TypedData\DataDefinitionInterface $data_definition */

    $attributes = [
      'data_type' => $data_definition->getDataType(),
      'label' => $data_definition->getLabel(),
      'description' => $data_definition->getDescription(),
      'list' => $data_definition->isList(),
      'read_only' => $data_definition->isReadOnly(),
      'computed' => $data_definition->isComputed(),
      'required' => $data_definition->isRequired(),
      'settings' => $data_definition->getSettings(),
      'constraints' => $data_definition->getConstraints(),
      'internal' => $data_definition->isInternal(),
    ];

    return $attributes;
  }

}
