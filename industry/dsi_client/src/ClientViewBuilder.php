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
      $section = new Section('layout_twocol_bricks', [], []);
      $section = $section->toRenderArray();
      $section['top']['client'] = $build;
      return $section;
    }
    return $build;
  }
}
