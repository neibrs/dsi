<?php

/**
 * @file
 * Contains dsi_hardware.page.inc.
 *
 * Page callback for Hardware entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Hardware templates.
 *
 * Default template: dsi_hardware.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dsi_hardware(array &$variables) {
  // Fetch Hardware Entity Object.
  $dsi_hardware = $variables['elements']['#dsi_hardware'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
