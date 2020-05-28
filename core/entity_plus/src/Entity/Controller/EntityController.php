<?php

namespace Drupal\entity_plus\Entity\Controller;

use Drupal\Core\Entity\Controller\EntityController as EntityControllerBase;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;

/**
 * It was extended in the following areas:
 * add_page: Support bundle which does ot have the add_form routing.
 * add_page: Filter by target_entity_type.
 * editTitle: Remove html code.
 */
class EntityController extends EntityControllerBase {

  public function bundleTitle(RouteMatchInterface $route_match, $entity_type_id, $bundle_parameter) {
    $bundles = $this->entityTypeBundleInfo->getBundleInfo($entity_type_id);
    // If the entity has bundle entities, the parameter might have been upcasted
    // so fetch the raw parameter.
    $bundle = $route_match->getRawParameter($bundle_parameter);
    if ((count($bundles) > 1) && isset($bundles[$bundle])) {
      return $bundles[$bundle]['label'];
    }
    // If the entity supports bundles generally, but only has a single bundle,
    // the bundle is probably something like 'Default' so that it preferable to
    // use the entity type label.
    else {
      $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
      return $entity_type->getLabel();
    }
  }

  /**
   * Some entity with bundle which does have the add_form routing, override the function and remove it.
   */
  public function addPage($entity_type_id) {
    $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
    $bundles = $this->entityTypeBundleInfo->getBundleInfo($entity_type_id);
    $bundle_key = $entity_type->getKey('bundle');
    $bundle_entity_type_id = $entity_type->getBundleEntityType();
    $build = [
      '#theme' => 'entity_add_list',
      '#bundles' => [],
    ];
    if ($bundle_entity_type_id) {
      $bundle_argument = $bundle_entity_type_id;
      $bundle_entity_type = $this->entityTypeManager->getDefinition($bundle_entity_type_id);
      $bundle_entity_type_label = $bundle_entity_type->getLowercaseLabel();
      $build['#cache']['tags'] = $bundle_entity_type->getListCacheTags();

      // Build the message shown when there are no bundles.
      /*$link_text = $this->t('Add a new @entity_type.', ['@entity_type' => $bundle_entity_type_label]);
      $link_route_name = 'entity.' . $bundle_entity_type->id() . '.add_form';
      $build['#add_bundle_message'] = $this->t('There is no @entity_type yet. @add_link', [
        '@entity_type' => $bundle_entity_type_label,
        '@add_link' => Link::createFromRoute($link_text, $link_route_name)->toString(),
      ]);*/
      // Filter out the bundles the user doesn't have access to.
      $access_control_handler = $this->entityTypeManager->getAccessControlHandler($entity_type_id);
      foreach ($bundles as $bundle_name => $bundle_info) {
        $access = $access_control_handler->createAccess($bundle_name, NULL, [], TRUE);
        if (!$access->isAllowed()) {
          unset($bundles[$bundle_name]);
        }
        $this->renderer->addCacheableDependency($build, $access);
      }
      // Add descriptions from the bundle entities.
      $bundles = $this->loadBundleDescriptions($bundles, $bundle_entity_type);
    }
    else {
      $bundle_argument = $bundle_key;
    }

    $form_route_name = 'entity.' . $entity_type_id . '.add_form';
    // Redirect if there's only one bundle available.
    if (count($bundles) == 1) {
      $bundle_names = array_keys($bundles);
      $bundle_name = reset($bundle_names);
      return $this->redirect($form_route_name, [$bundle_argument => $bundle_name]);
    }
    // Prepare the #bundles array for the template.
    foreach ($bundles as $bundle_name => $bundle_info) {
      // 将路由参数传递下去.
      $parameters = \Drupal::routeMatch()->getParameters();
      $route_parameters = [];
      foreach ($parameters->all() as $key => $value) {
        if (!in_array($key, ['entity_type_id', 'target_entity_type'])) {
          $route_parameters[$key] = $value;
        }
      }
      $route_parameters[$bundle_argument] = $bundle_name;
      $build['#bundles'][$bundle_name] = [
        'label' => $bundle_info['label'],
        'description' => isset($bundle_info['description']) ? $bundle_info['description'] : '',
        'add_link' => Link::createFromRoute($bundle_info['label'], $form_route_name, $route_parameters),
      ];
    }

    return $build;
  }
  
  /**
   * {@inheritdoc}
   */
  public function editTitle(RouteMatchInterface $route_match, EntityInterface $_entity = NULL) {
    if ($entity = $this->doGetEntity($route_match, $_entity)) {
      return $this->t('Edit @label', ['@label' => $entity->label()]);
    }
  }
}
