<?php

namespace Drupal\data_model\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\FieldableEntityInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Returns responses for Data model routes.
 */
class DataModelController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, EntityFieldManagerInterface $entity_field_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityFieldManager = $entity_field_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('entity_field.manager')
    );
  }

  public function reference($entity_type_id) {
    $entity_types = [$entity_type_id];
    return [
      '#markup' => '<div id="data-model"></div>',
      '#attached' => [
        'library' => ['data_model/data_model'],
        'drupalSettings' => ['data_model' => ['entity_types' => $entity_types]],
      ],
    ];
  }

  public function referencedBy($entity_type_id) {
    $entity_types = [$entity_type_id => $entity_type_id];

    $definitions = $this->entityTypeManager->getDefinitions();
    foreach ($definitions as $definition) {
      if ($definition->entityClassImplements(FieldableEntityInterface::class)) {
        $key = $definition->id();
        $fields = $this->entityFieldManager->getFieldStorageDefinitions($key);
        foreach ($fields as $field) {
          if ($field->getSetting('target_type') == $entity_type_id) {
            $entity_types[$key] = $key;
            continue;
          }
        }
      }
    }
    return [
      '#markup' => '<div id="data-model"></div>',
      '#attached' => [
        'library' => ['data_model/data_model'],
        'drupalSettings' => ['data_model' => ['entity_types' => array_values($entity_types)]],
      ],
    ];
  }

}
