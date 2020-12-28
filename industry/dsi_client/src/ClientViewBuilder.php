<?php

namespace Drupal\dsi_client;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;
use Drupal\layout_builder\Section;

/**
 * View builder handler for client.
 */
class ClientViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritDoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $build = parent::view($entity, $view_mode, $langcode);

    if ($view_mode == 'full') {
      $section = new Section('layout_onecol', [], []);
      $section = $section->toRenderArray();
      $section['content']['client'] = $build;
      return $section;
    }
    return $build;
  }

  /**
   * {@inheritDoc}
   */
  public function buildComponents(array &$build, array $entities, array $displays, $view_mode) {
    if (empty($entities)) {
      return;
    }
    parent::buildComponents($build, $entities, $displays, $view_mode);

    foreach ($entities as $id => $entity) {
      $bundle = $entity->bundle();
      $display = $displays[$bundle];

      if ($display->getComponent('client')) {
        $x = 'a';
      }

    }

  }

}
