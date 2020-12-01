<?php

/**
 * @file
 * Contains dsi_contract.page.inc.
 *
 * Page callback for Contract entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Contract templates.
 *
 * Default template: dsi_contract.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dsi_contract(array &$variables) {
  // Fetch Contract Entity Object.
  $dsi_contract = $variables['elements']['#dsi_contract'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
