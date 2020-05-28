<?php

namespace Drupal\small_title\Entity\Controller;

use Drupal\Core\Entity\Controller\EntityViewController as EntityViewControllerBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * Remove buildTitle
 */
class EntityViewController extends EntityViewControllerBase {

  /**
   * Remove buildTitle
   */
  public function view(EntityInterface $_entity, $view_mode = 'full') {
    $page = $this->entityManager
      ->getViewBuilder($_entity->getEntityTypeId())
      ->view($_entity, $view_mode);

    // $page['#pre_render'][] = [$this, 'buildTitle'];
    $page['#entity_type'] = $_entity->getEntityTypeId();
    $page['#' . $page['#entity_type']] = $_entity;

    return $page;
  }

}
