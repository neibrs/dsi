<?php

/**
 * @file
 * Contains grant.page.inc.
 *
 * Page callback for Grants.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Grant templates.
 *
 * Default template: grant.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_grant(array &$variables) {
  // Fetch Grant Entity Object.
  $grant = $variables['elements']['#grant'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
