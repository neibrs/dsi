<?php

namespace Drupal\entity_log\Controller;

use Drupal\Core\Controller\ControllerBase;

class EntityLogController extends ControllerBase {

  public function getTitle($entity_type_id, $entity_id) {

    /** @var \Drupal\Core\Entity\EntityStorageInterface $entity_type */
    $storage = $this->entityTypeManager->getStorage($entity_type_id);

    if (!$storage) {
      return $this->t('The entity type not found.');
    }

    $entity_type = $storage->getEntityType()->getBundleLabel();

    $entity = $storage->load($entity_id);
    if (!$entity) {
      return $this->t('The entity of %entity_type not found.', ['%entity_type' => $entity_type]);
    }

    return $this->t('%entity_type : %entity_id change log.', [
      '%entity_type' => $entity_type,
      '%entity_id' => $entity->label(),
    ]);

  }
}
