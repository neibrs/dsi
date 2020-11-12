<?php

namespace Drupal\lookup;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\entity_plus\Entity\Routing\DefaultHtmlRouteProvider;
use Drupal\entity_plus\Entity\Controller\EntityController;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for Lookup type entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class LookupTypeHtmlRouteProvider extends DefaultHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    $route = new Route($entity_type->getLinkTemplate('lookup'));
    $route
      ->addDefaults([
        '_entity_list' => 'lookup',
        '_title_callback' => EntityController::class . '::bundleTitle',
        'entity_type_id' => 'lookup',
        'bundle_parameter' => 'lookup_type',
      ])
      ->setRequirement('_permission', 'view lookups');
    $collection->add("entity.lookup_type.lookup", $route);

    return $collection;
  }

}
