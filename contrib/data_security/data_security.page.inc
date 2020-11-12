<?php

/**
 * @file
 * Contains data_security.page.inc.
 *
 * Page callback for Data securities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Data security templates.
 *
 * Default template: data_security.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_data_security(array &$variables) {
  // Fetch DataSecurity Entity Object.
  $data_security = $variables['elements']['#data_security'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
