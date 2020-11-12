<?php

/**
 * @file
 * Contains dsi_record.page.inc.
 *
 * Page callback for Record entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Record templates.
 *
 * Default template: dsi_record.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dsi_record(array &$variables) {
  // Fetch Record Entity Object.
  $dsi_record = $variables['elements']['#dsi_record'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
