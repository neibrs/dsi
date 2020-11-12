<?php

namespace Drupal\entity_plus\Entity\Controller;

use Drupal\Core\Entity\Controller\EntityViewController as EntityViewControllerBase;
use Drupal\Core\Entity\EntityInterface;

/**
 * Remove the buildTitle pre_render.
 */
class EntityViewController extends EntityViewControllerBase {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $_entity, $view_mode = 'full') {
    $page = $this->entityTypeManager
      ->getViewBuilder($_entity->getEntityTypeId())
      ->view($_entity, $view_mode);

    // Remove the bulidTitle pre_render.
    // $page['#pre_render'][] = [$this, 'buildTitle'];
    $page['#entity_type'] = $_entity->getEntityTypeId();
    $page['#' . $page['#entity_type']] = $_entity;

    return $page;
  }

}
