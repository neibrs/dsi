<?php

/**
 * @file
 * Contains person_address.page.inc.
 *
 * Page callback for Person address entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Person address templates.
 *
 * Default template: person_address.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_person_address(array &$variables) {
  // Fetch PersonAddress Entity Object.
  $person_address = $variables['elements']['#person_address'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
