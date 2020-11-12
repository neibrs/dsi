<?php

/**
 * @file
 * Contains organization.page.inc.
 *
 * Page callback for organizations.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Organization templates.
 *
 * Default template: organization.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_organization(array &$variables) {
  // Fetch Organization Entity Object.
  $organization = $variables['elements']['#organization'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
