<?php

/**
 * @file
 * Contains responsibility.page.inc.
 *
 * Page callback for Responsibility entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Responsibility templates.
 *
 * Default template: responsibility.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_responsibility(array &$variables) {
  // Fetch Responsibility Entity Object.
  $responsibility = $variables['elements']['#responsibility'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
