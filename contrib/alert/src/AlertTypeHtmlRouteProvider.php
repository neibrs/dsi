<?php

namespace Drupal\alert;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\entity_plus\Entity\Routing\DefaultHtmlRouteProvider;

/**
 * Provides routes for Alert type entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class AlertTypeHtmlRouteProvider extends DefaultHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    // Provide your custom entity routes here.

    return $collection;
  }

}
