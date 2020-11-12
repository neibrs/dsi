<?php

/**
 * @file
 * Contains person_email.page.inc.
 *
 * Page callback for Person email entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Person email templates.
 *
 * Default template: person_email.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_person_email(array &$variables) {
  // Fetch PersonEmail Entity Object.
  $person_email = $variables['elements']['#person_email'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
