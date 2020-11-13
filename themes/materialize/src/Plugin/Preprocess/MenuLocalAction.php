<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Utility\Variables;

/**
 * Pre-processes variables for the "menu_local_action" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("menu_local_action")
 */
class MenuLocalAction extends PreprocessBase implements PreprocessInterface {

  /**
   * {@inherit}
   */
  public function preprocess(array &$variables, $hook, array $info) {
    $classes = $variables['link']['#options']['attributes']['class'];
    $classes = array_diff($classes, [
      'button',
      'button-action',
      'button--primary',
      'button--small',
    ]);
    $classes[] = 'waves-effect waves-light btn-small';
    $variables['link']['#options']['attributes']['class'] = $classes;
  }

}
