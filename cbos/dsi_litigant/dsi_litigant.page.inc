<?php

/**
 * @file
 * Contains dsi_litigant.page.inc.
 *
 * Page callback for Litigant entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Litigant templates.
 *
 * Default template: dsi_litigant.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dsi_litigant(array &$variables) {
  // Fetch Litigant Entity Object.
  $dsi_litigant = $variables['elements']['#dsi_litigant'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
