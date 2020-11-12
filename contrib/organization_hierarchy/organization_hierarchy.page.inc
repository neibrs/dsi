<?php

/**
 * @file
 * Contains organization_hierarchy.page.inc.
 *
 * Page callback for Organization hierarchy entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Organization hierarchy templates.
 *
 * Default template: organization_hierarchy.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_organization_hierarchy(array &$variables) {
  // Fetch OrganizationHierarchy Entity Object.
  $organization_hierarchy = $variables['elements']['#organization_hierarchy'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
