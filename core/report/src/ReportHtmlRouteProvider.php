<?php

namespace Drupal\report;

use Drupal\Core\Entity\Controller\EntityController;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\entity_plus\Entity\Routing\DefaultHtmlRouteProvider;
use Drupal\report\Controller\ReportController;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for Report entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class ReportHtmlRouteProvider extends DefaultHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    // Provide your custom entity routes here.
    return $collection;
  }

  /**
   * {@inheritdoc}
   */
  protected function getAddPageRoute(EntityTypeInterface $entity_type) {
    $route = new Route($entity_type->getLinkTemplate('add-page'));
    $route->setDefault('_controller', ReportController::class . '::addPage');
    $route->setDefault('_title_callback', EntityController::class . '::addTitle');
    $route->setDefault('entity_type_id', $entity_type->id());
    $route->setRequirement('_entity_create_any_access', $entity_type->id());

    return $route;
  }
}
