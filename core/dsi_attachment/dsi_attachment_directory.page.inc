<?php

/**
 * @file
 * Contains dsi_attachment_directory.page.inc.
 *
 * Page callback for Attachment directory entities.
 */

use Drupal\Core\Render\Element;

/**
 * Prepares variables for Attachment directory templates.
 *
 * Default template: dsi_attachment_directory.html.twig.
 *
 * @param array $variables
 *   An associative array containing:
 *   - elements: An associative array containing the user information and any
 *   - attributes: HTML attributes for the containing element.
 */
function template_preprocess_dsi_attachment_directory(array &$variables) {
  // Fetch AttachmentDirectory Entity Object.
  $dsi_attachment_directory = $variables['elements']['#dsi_attachment_directory'];

  // Helpful $content variable for templates.
  foreach (Element::children($variables['elements']) as $key) {
    $variables['content'][$key] = $variables['elements'][$key];
  }
}
