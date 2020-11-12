<?php

/**
 * @file
 * Contains person_phone.page.inc.
 *
 * Page callback for Person phone entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Person phone templates.
 *
 * Default template: person_phone.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_person_phone(array &$variables) {
  // Fetch PersonPhone Entity Object.
  $person_phone = $variables['elements']['#person_phone'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
