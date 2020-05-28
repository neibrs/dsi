<?php

/**
 * @file
 * Contains layout_template.page.inc.
 *
 * Page callback for Layout template entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Layout template templates.
 *
 * Default template: layout_template.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_layout_template(array &$variables) {
  // Fetch LayoutTemplate Entity Object.
  $layout_template = $variables['elements']['#layout_template'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
