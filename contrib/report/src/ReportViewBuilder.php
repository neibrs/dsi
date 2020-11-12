<?php

namespace Drupal\report;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityViewBuilder;

class ReportViewBuilder extends EntityViewBuilder {

  /**
   * {@inheritdoc}
   */
  public function view(EntityInterface $entity, $view_mode = 'full', $langcode = NULL) {
    $build = $this->viewMultiple([$entity], $view_mode, $langcode);
    return reset($build);
  }

  /**
   * {@inheritdoc}
   */
  public function viewMultiple(array $entities = [], $view_mode = 'full', $langcode = NULL) {
    $build = [];

    /** @var \Drupal\report\Entity\ReportInterface[] $entities */
    foreach ($entities as $entity) {
      $plugin = $entity->getPlugin();
      $build[$entity->id()] = $plugin->build($entity, $view_mode);
    }

    return $build;
  }

}
