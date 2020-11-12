<?php

namespace Drupal\dsi_classification;

use Drupal\Core\Entity\EntityTypeManagerInterface;

class ClassificationRepository implements ClassificationRepositoryInterface {

  /**
 * @var \Drupal\Core\Entity\EntityTypeManagerInterface */
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritDoc}
   */
  public function getClassificationAllowedValues($entity_type_id = NULL) {
    if (empty($entity_type_id)) {
      return [];
    }

    $classifications = $this->entityTypeManager->getStorage('dsi_classification')
      ->loadByProperties([
        'target_entity_type_id' => $entity_type_id,
      ]);
    $classifications = array_map(function ($classification) {
      return $classification->label();
    }, $classifications);

    return $classifications;
  }

}
