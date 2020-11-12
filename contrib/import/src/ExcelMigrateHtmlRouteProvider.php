<?php

namespace Drupal\import;

use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\entity_plus\Entity\Routing\DefaultHtmlRouteProvider;
use Symfony\Component\Routing\Route;

/**
 * Provides routes for Excel migrate entities.
 *
 * @see Drupal\Core\Entity\Routing\AdminHtmlRouteProvider
 * @see Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider
 */
class ExcelMigrateHtmlRouteProvider extends DefaultHtmlRouteProvider {

  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $collection = parent::getRoutes($entity_type);

    $entity_type_id = $entity_type->id();

    if ($import_form_route = $this->getImportFormRoute($entity_type)) {
      $collection->add("entity.{$entity_type_id}.import_form", $import_form_route);
    }

    return $collection;
  }

  protected function getImportFormRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('import-form')) {
      $route = new Route($entity_type->getLinkTemplate('import-form'));
      $route->setDefault('_form', 'Drupal\import\Form\ExcelImportForm')
        ->setRequirement('_access', 'TRUE');

      return $route;
    }
  }

}
