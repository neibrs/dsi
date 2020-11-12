<?php

namespace Drupal\materialize\Plugin\Preprocess;

use Drupal\materialize\Utility\Variables;

/**
 * Pre-processes variables for the "html" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("html")
 */
class Html extends PreprocessBase implements PreprocessInterface {

  public function preprocessVariables(Variables $variables) {
    $x = 'a';
    // Starter page.
    // Html attributes
    $language_interface = \Drupal::languageManager()->getCurrentLanguage();
    $variables['html_attributes']->setAttribute('data-textdirection', $language_interface->getDirection());
    $variables['html_attributes']->addClass('loading');

    // Body attributes
    $variables['attributes']['class'][] = 'vertical-layout vertical-menu-collapsible page-header-dark vertical-modern-menu preload-transitions';
    $variables['attributes']['data-open'] = 'click';
    $variables['attributes']['data-menu'] = 'vertical-modern-menu';

    $route_name = \Drupal::routeMatch()->getRouteName();
    switch ($route_name) {
      case 'user.login':
        $variables['attributes']['class'][] = '1-column login-bg blank-page blank-page';
        $variables['attributes']['data-col'] = '1-column';
        break;

      default:
        $variables['attributes']['class'][] = '2-columns';
        $variables['attributes']['data-col'] = '2-columns';
    }
  }

}
