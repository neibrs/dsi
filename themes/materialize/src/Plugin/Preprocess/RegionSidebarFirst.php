<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Utility\Element;
use Drupal\materialize\Utility\Variables;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("region__sidebar_first")
 */
class RegionSidebarFirst extends PreprocessBase {

  public function preprocessVariables(Variables $variables) {
    $x = 'a';
  }

  public function preprocess(array &$variables, $hook, array $info) {
    $x = 'a';
  }

  public function preprocessAttributes() {
    $x = 'a';
  }

  public function preprocessDescription() {
    $x = 'a';
  }

  public function preprocessElement(Element $element, Variables $variables) {
    $x = 'a';
  }

}
