<?php

/**
 * @file
 * Contains dsi_project.page.inc.
 *
 * Page callback for Project entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Project templates.
 *
 * Default template: dsi_project.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dsi_project(array &$variables) {
  // Fetch Project Entity Object.
  $dsi_project = $variables['elements']['#dsi_project'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
