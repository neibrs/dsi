<?php

namespace Drupal\entity_plus\Entity\Routing;

use Drupal\Core\Config\Entity\ConfigEntityTypeInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Entity\Routing\DefaultHtmlRouteProvider as DefaultHtmlRouteProviderBase;
use Drupal\entity_plus\Entity\Controller\EntityController;
use Symfony\Component\Routing\Route;

class DefaultHtmlRouteProvider extends DefaultHtmlRouteProviderBase {
  
  /**
   * {@inheritdoc}
   */
  public function getRoutes(EntityTypeInterface $entity_type) {
    $routes = parent::getRoutes($entity_type);
    
    if ($entity_type->hasKey('status') && $entity_type instanceof ConfigEntityTypeInterface) {
      $entity_type_id = $entity_type->id();
      
      if ($enable_route = $this->getEnableRoute($entity_type)) {
        $routes->add("entity.{$entity_type_id}.enable", $enable_route);
      }
      if ($disable_route = $this->getDisableRoute($entity_type)) {
        $routes->add("entity.{$entity_type_id}.disable", $disable_route);
      }
    }
    
    return $routes;
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getAddPageRoute(EntityTypeInterface $entity_type) {
    $route = parent::getAddPageRoute($entity_type);

    if ($route) {
      // Support bundle which does not have the add_form routing.
      $route->setDefault('_controller', EntityController::class . '::addPage');
    }

    return $route;
  }
  
  /**
   * {@inheritdoc}
   */
  protected function getEditFormRoute(EntityTypeInterface $entity_type) {
    $route = parent::getEditFormRoute($entity_type);
    
    if ($route) {
      // Remove HTML code for editTitle.
      $route->setDefault('_title_callback', EntityController::class . '::editTitle');
    }
    
    return $route;
  }
  
  protected function getEnableRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('enable') && $entity_type->getKey('status')) {
      $entity_type_id = $entity_type->id();
      
      $route = new Route($entity_type->getLinkTemplate('enable'));
      $route->setDefault('_perform_operation', "${entity_type_id}.enable");
      $route->setOption('parameters', [
        $entity_type_id => ['type' => 'entity:' . $entity_type_id],
      ]);
      $route->setRequirement('_entity_access', "${entity_type_id}.enable");
      $route->setRequirement('_csrf_token', 'TRUE');
      
      return $route;
    }
  }
  
  protected function getDisableRoute(EntityTypeInterface $entity_type) {
    if ($entity_type->hasLinkTemplate('disable') && $entity_type->getKey('status')) {
      $entity_type_id = $entity_type->id();
      
      $route = new Route($entity_type->getLinkTemplate('disable'));
      $route->setDefault('_perform_operation', "${entity_type_id}.disable");
      $route->setOption('parameters', [
        $entity_type_id => ['type' => 'entity:' . $entity_type_id],
      ]);
      $route->setRequirement('_entity_access', "${entity_type_id}.disable");
      $route->setRequirement('_csrf_token', 'TRUE');
      
      return $route;
    }
  }
}
