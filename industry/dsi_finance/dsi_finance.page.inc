<?php
//
///**
// * @file
// * Contains dsi_finance.page.inc.
// *
// * Page callback for Finance entities.
// */
//
//use Drupal\Core\Render\Element;
//
///**
// * Prepares variables for Finance templates.
// *
// * Default template: dsi_finance.html.twig.
// *
// * @param array $variables
// *   An associative array containing:
// *   - elements: An associative array containing the user information and any
// *   - attributes: HTML attributes for the containing element.
// */
//function template_preprocess_dsi_finance(array &$variables) {
//  // Fetch Finance Entity Object.
//  $dsi_finance = $variables['elements']['#dsi_finance'];
//
//  // Helpful $content variable for templates.
//  foreach (Element::children($variables['elements']) as $key) {
//    $variables['content'][$key] = $variables['elements'][$key];
//  }
//}
