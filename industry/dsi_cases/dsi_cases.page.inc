<?php

/**
 * @file
 * Contains dsi_cases.page.inc.
 *
 * Page callback for Cases entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Cases templates.
 *
 * Default template: dsi_cases.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dsi_cases(array &$variables) {
  // Fetch Cases Entity Object.
  $dsi_cases = $variables['elements']['#dsi_cases'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
