<?php

/**
 * @file
 * Contains identification_information.page.inc.
 *
 * Page callback for Identification information entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Identification information templates.
 *
 * Default template: identification_information.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_identification_information(array &$variables) {
  // Fetch IdentificationInformation Entity Object.
  $identification_information = $variables['elements']['#identification_information'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
