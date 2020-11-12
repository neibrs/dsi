<?php

/**
 * @file
 * Contains administrative_area.page.inc.
 *
 * Page callback for Administrative areas.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Administrative area templates.
 *
 * Default template: administrative_area.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_administrative_area(array &$variables) {
  // Fetch AdministrativeArea Entity Object.
  $administrative_area = $variables['elements']['#administrative_area'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
