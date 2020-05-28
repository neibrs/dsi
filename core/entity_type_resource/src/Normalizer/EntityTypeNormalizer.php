<?php

namespace Drupal\entity_type_resource\Normalizer;

use Drupal\Core\Entity\ContentEntityType;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\serialization\Normalizer\NormalizerBase;

/**
 * Normalizes entity type objects into an array structure.
 */
class EntityTypeNormalizer extends NormalizerBase {

  /**
   * The interface or class that this Normalizer supports.
   *
   * @var array
   */
  protected $supportedInterfaceOrClass = [EntityTypeInterface::class];

  /**
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Constructs an EntityTypeNormalizer object.
   *
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager
   *   The entity field manager.
   */
  public function __construct(EntityFieldManagerInterface $entity_field_manager) {
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public function normalize($entity_type, $format = NULL, array $context = []) {
    /** @var \Drupal\Core\Entity\EntityTypeInterface $entity_type */

    $attributes = [
      'id' => $entity_type->id(),
      'links' => $entity_type->getLinkTemplates(),
      'bundle_entity_type' => $entity_type->getBundleEntityType(),
      'bundle_of' => $entity_type->getBundleOf(),
      'bundle_label' => $entity_type->getBundleLabel(),
      'translatable' => $entity_type->isTranslatable(),
      'revisionable' => $entity_type->isRevisionable(),
      'label' => $entity_type->getLabel(),
      'group' => $entity_type->getGroup(),
      'group_label' => $entity_type->getGroupLabel(),
    ];

    if ($entity_type instanceof ContentEntityType) {
      $definitions = $this->entityFieldManager->getBaseFieldDefinitions($entity_type->id());
      foreach ($definitions as $name => $definition) {
        $attributes['base_fields'][$name] = $this->serializer->normalize($definition, $format, $context);
      }
    }

    return $attributes;
  }

}
