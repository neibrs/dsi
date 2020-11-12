<?php

/**
 * @file
 * Contains instance_set.page.inc.
 *
 * Page callback for Instance set entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Instance set templates.
 *
 * Default template: instance_set.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_instance_set(array &$variables) {
  // Fetch InstanceSet Entity Object.
  $instance_set = $variables['elements']['#instance_set'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
