<?php

/**
 * @file
 * Contains dsi_device.page.inc.
 *
 * Page callback for Devices.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Device templates.
 *
 * Default template: dsi_device.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dsi_device(array &$variables) {
  // Fetch Device Entity Object.
  $dsi_device = $variables['elements']['#dsi_device'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
