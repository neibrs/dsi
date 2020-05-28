<?php

namespace Drupal\entity_plus\Entity\Controller;

use Drupal\Core\Entity\Controller\EntityListController as EntityListControllerBase;

class EntityListController extends EntityListControllerBase {

  public function listing($entity_type) {
    $build = parent::listing($entity_type);

    \Drupal::moduleHandler()->alter('entity_list', $build, $entity_type);

    return $build;
  }

}
