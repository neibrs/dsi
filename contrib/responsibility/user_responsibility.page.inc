<?php

/**
 * @file
 * Contains user_responsibility.page.inc.
 *
 * Page callback for User responsibilities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for User responsibility templates.
 *
 * Default template: user_responsibility.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_user_responsibility(array &$variables) {
  // Fetch UserResponsibility Entity Object.
  $user_responsibility = $variables['elements']['#user_responsibility'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
