<?php

/**
 * @file
 * Contains lookup.page.inc.
 *
 * Page callback for Lookup entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Lookup templates.
 *
 * Default template: lookup.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_lookup(array &$variables) {
  // Fetch Lookup Entity Object.
  $lookup = $variables['elements']['#lookup'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
