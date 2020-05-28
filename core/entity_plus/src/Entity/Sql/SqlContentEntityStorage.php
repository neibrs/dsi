<?php

namespace Drupal\entity_plus\Entity\Sql;

use Drupal\Core\Entity\ContentEntityStorageBase;
use Drupal\Core\Entity\Sql\SqlContentEntityStorage as SqlContentEntityStorageBase;
use Drupal\entity_plus\Entity\Sql\SqlEntityStorageInterface;

class SqlContentEntityStorage extends SqlContentEntityStorageBase implements SqlEntityStorageInterface {

  /**
   * @return \Drupal\entity_plus\Entity\Sql\SqlEntityStorageInterface
   */
  public function loadOrCreate(array $values) {
    $entities = $this->loadByProperties($values);
    $entity = reset($entities);
    if (!$entity) {
      $entity = $this->create($values);
    }

    return $entity;
  }
}
