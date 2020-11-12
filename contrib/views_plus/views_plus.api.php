<?php

/**
 * @file
 */

use Drupal\Core\Entity\EntityInterface;
use Drupal\views\ViewExecutable;

function hook_views_tree_data_url_alter(&$data_url, ViewExecutable $view, EntityInterface $entity) {
  if ($view->id() == 'bom_component') {
    if ($bom = $entity->getChildBom()) {
      $data_url = \Drupal\Core\Url::fromRoute('entity.bom.component', ['bom' => $bom->id()])->toString();
    }
  }
}

function hook_views_plus_view_alter(array &$build, $view_id, $display_id) {
  // TODO
}
