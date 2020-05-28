<?php

/**
 * @file
 */

use Drupal\Core\Cache\Cache;
use Drupal\views\ViewExecutable;

/**
 * Implements hook_views_pre_build().
 */
function layout_template_views_pre_build(ViewExecutable $view) {
  /** @var \Drupal\layout_template\LayoutTemplateManagerInterface $layout_template_manager */
  $layout_template_manager = \Drupal::service('layout_template.manager');
  if ($layout_template = $layout_template_manager->currentLayoutTemplate('view', $view->id())) {
    $configuration = $layout_template->getConfiguration();

    // Hide filters by configuration.
    foreach ($view->filter as $key => $filter) {
      if (isset($configuration['filters'][$key]['visible']) && !$configuration['filters'][$key]['visible']) {
        unset($view->filter[$key]);
      }
    }
  }
}

/**
 * Implements hook_views_post_build().
 */
function layout_template_views_post_build(ViewExecutable $view) {
  /** @var \Drupal\layout_template\LayoutTemplateManagerInterface $layout_template_manager */
  $layout_template_manager = \Drupal::service('layout_template.manager');
  if ($layout_template = $layout_template_manager->currentLayoutTemplate('view', $view->id())) {
    $configuration = $layout_template->getConfiguration();

    // Setting fields by configuration.
    foreach ($view->field as $key => $field) {
      if (isset($configuration['fields'][$key]['visible']) && !$configuration['fields'][$key]['visible']) {
        $view->field[$key]->options['exclude'] = TRUE;
      }
      if (isset($configuration['fields'][$key]['label']) && $configuration['fields'][$key]['label']) {
        $view->field[$key]->options['label'] = $configuration['fields'][$key]['label'];
      }
    }

    // Set the cache tags.
    $old_tags = isset($view->element['#cache']['tags']) ? $view->element['#cache']['tags'] : [];
    $view->element['#cache']['tags'] = Cache::mergeTags($layout_template->getCacheTags(), $old_tags);
    $old_contexts = $view->element['#cache']['contexts'];
    $view->element['#cache']['contexts'] = Cache::mergeContexts(['url', 'url.query_args'], $old_contexts);
  }

}
