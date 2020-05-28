<?php

namespace Drupal\layout_template\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * @Block(
 *   id = "layout_template_block",
 *   admin_label = @Translation("Layout template"),
 * )
 */
class LayoutTemplateBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $route_match = \Drupal::routeMatch();
    $route = $route_match->getRouteObject();
    $defaults = $route->getDefaults();

    // For entity form display.
    if (isset($defaults['_entity_form']) && $defaults['_entity_form'] != 'layout_template.edit') {
      $type = 'entity_form_display';

      /** @var \Drupal\layout_template\LayoutTemplateManagerInterface $layout_template_manager */
      $layout_template_manager = \Drupal::service('layout_template.manager');
      $entity_form_display = $layout_template_manager->getEntityFormDisplayFromRoute();
      if ($entity_form_display) {
        $related_config = $entity_form_display->id();
      }
    }
    // For views.
    elseif (isset($defaults['view_id'])) {
      $type = 'view';
      $related_config = $defaults['view_id'];
    }

    if (isset($related_config)) {
      return \Drupal::formBuilder()->getForm('\Drupal\layout_template\Form\LayoutTemplateSwitchForm', $type, $related_config);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    return Cache::mergeContexts([
      'languages:language_interface',
      'url',
      'url.query_args',
      'user.permissions',
    ], parent::getCacheContexts());
  }

}
