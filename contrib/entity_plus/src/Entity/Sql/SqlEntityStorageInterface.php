<?php

namespace Drupal\entity_plus\Entity\Sql;

use Drupal\Core\Entity\ContentEntityStorageInterface;

interface SqlEntityStorageInterface extends ContentEntityStorageInterface {

  /**
   * @return \Drupal\entity_plus\Entity\Sql\SqlEntityStorageInterface
   */
  public function loadOrCreate(array $values);

}
