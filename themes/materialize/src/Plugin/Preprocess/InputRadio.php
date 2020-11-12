<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Utility\Variables;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("input__radio")
 */
class InputRadio extends PreprocessBase {

  /**
   * {@inheritdoc}
   */
  public function preprocessVariables(Variables $variables) {
    // Setup default attributes.
    $x = 'a';
    //    $variables->getAttributes($variables::NAVBAR);
    //    $variables->getAttributes($variables::HEADER);
    //    $variables->getAttributes($variables::CONTENT);
    //    $variables->getAttributes($variables::FOOTER);
    //    $this->preprocessAttributes();
  }

}
