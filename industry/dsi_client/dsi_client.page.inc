<?php

/**
 * @file
 * Contains dsi_client.page.inc.
 *
 * Page callback for Client entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Client templates.
 *
 * Default template: dsi_client.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dsi_client(array &$variables) {
  // Fetch Client Entity Object.
  $dsi_client = $variables['elements']['#dsi_client'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
