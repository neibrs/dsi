<?php

namespace Drupal\layout_template;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\ContentEntityTypeInterface;

class LayoutTemplateManager implements LayoutTemplateManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function getEntityFormDisplay($entity_type, $bundle, $form_mode) {
    $entity_type_definition = \Drupal::entityTypeManager()->getDefinition($entity_type);
    if (!$entity_type_definition instanceof ContentEntityTypeInterface) {
      return;
    }

    $entity_form_display_storage = \Drupal::entityTypeManager()->getStorage('entity_form_display');
    $entity_form_display = $entity_form_display_storage->load($entity_type . '.' . $bundle . '.' . $form_mode);
    if (!$entity_form_display && $form_mode != 'default') {
      $entity_form_display = $entity_form_display_storage->load($entity_type . '.' . $bundle . '.default');
    }
    if (!$entity_form_display) {
      $entity_form_display = $entity_form_display_storage->create([
        'targetEntityType' => $entity_type,
        'bundle' => $bundle,
        'mode' => $form_mode,
        'status' => TRUE,
      ]);
    }
    return $entity_form_display;
  }

  /**
   * {@inheritdoc}
   */
  public function getEntityFormDisplayFromRoute() {
    $route_match = \Drupal::routeMatch();
    $defaults = $route_match->getRouteObject()->getDefaults();

    if (!isset($defaults['_entity_form'])) {
      return NULL;
    }

    $bundle = NULL;
    list ($entity_type_id, $operation) = explode('.', $defaults['_entity_form']);
    if ($operation == 'add') {
      if (isset($defaults['bundle_parameter'])) {
        $bundle = $route_match->getRawParameter($defaults['bundle_parameter']);
      }
      else {
        $bundle = $entity_type_id;
      }
    }
    else {
      $parameters = $route_match->getParameters();
      foreach ($parameters as $parameter) {
        if ($parameter instanceof ContentEntityInterface) {
          $bundle = $parameter->bundle();
          break;
        }
      }
    }

    return $this->getEntityFormDisplay($entity_type_id, $bundle, $operation);
  }

  /**
   * {@inheritdoc}
   */
  public function currentLayoutTemplate($type, $related_config) {
    $layout_template = \Drupal::request()->query->get('layout_template');
    $key = 'layout_template_' . $type . '_' . $related_config;
    $session = \Drupal::request()->getSession();
    if (!empty($layout_template)) {
      $session->set($key, $layout_template);
    }
    else {
      if ($session->has($key)) {
        $layout_template = $session->get($key);
      }
      else {
        $layout_template_storage = \Drupal::entityTypeManager()->getStorage('layout_template');
        $query = $layout_template_storage->getQuery();
        $or = $query->orConditionGroup()
          ->condition('is_public', TRUE)
          ->condition('user_id', \Drupal::currentUser()->id());
        $query->condition($or);

        if ($type != NULL && $related_config != NULL) {
          $and = $query->andConditionGroup()
            ->condition('type', $type)
            ->condition('related_config', $related_config);
          $query->condition($and);
        }
        if ($ids = $query->execute()) {
          $layout_template = reset($ids);
        }
      }
    }

    if (!empty($layout_template)) {
      return \Drupal::entityTypeManager()->getStorage('layout_template')->load($layout_template);
    }
  }

}
