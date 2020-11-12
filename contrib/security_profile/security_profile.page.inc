<?php

/**
 * @file
 * Contains security_profile.page.inc.
 *
 * Page callback for Security profile entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Security profile templates.
 *
 * Default template: security_profile.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_security_profile(array &$variables) {
  // Fetch SecurityProfile Entity Object.
  $security_profile = $variables['elements']['#security_profile'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
