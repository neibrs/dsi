<?php

/**
 * @file
 * Contains dsi_purchased.page.inc.
 *
 * Page callback for Purchased entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Purchased templates.
 *
 * Default template: dsi_purchased.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dsi_purchased(array &$variables) {
  // Fetch Purchased Entity Object.
  $dsi_purchased = $variables['elements']['#dsi_purchased'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
