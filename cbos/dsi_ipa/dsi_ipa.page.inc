<?php

/**
 * @file
 * Contains dsi_ipa.page.inc.
 *
 * Page callback for IP Addresses.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for IP Address templates.
 *
 * Default template: dsi_ipa.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dsi_ipa(array &$variables) {
  // Fetch Ipa Entity Object.
  $dsi_ipa = $variables['elements']['#dsi_ipa'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
