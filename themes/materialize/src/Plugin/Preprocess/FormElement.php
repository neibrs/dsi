<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Utility\Variables;

/**
 * Pre-processes variables for the "form_element" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("form_element")
 */
class FormElement extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inherit}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $variables['materialize'] = FALSE;
    switch ($variables['type']) {
      case 'radio':
        $variables['materialize'] = TRUE;
        break;
    }
  }
}
