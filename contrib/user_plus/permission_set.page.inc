<?php

/**
 * @file
 * Contains permission_set.page.inc.
 *
 * Page callback for Permission sets.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Permission set templates.
 *
 * Default template: permission_set.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_permission_set(array &$variables) {
  // Fetch PermissionSet Entity Object.
  $permission_set = $variables['elements']['#permission_set'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
