<?php

namespace Drupal\materialize\Plugin\Preprocess;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("menu__role_menu")
 */
class MenuRoleMenu extends PreprocessBase {

  public function preprocess(array &$variables, $hook, array $info) {
    $variables['attributes']['class'][] = 'sidenav sidenav-collapsible leftside-navigation collapsible sidenav-fixed menu-shadow ps ps--active-y';
    $variables['attributes']['id'] = 'slide-out';
    $variables['attributes']['data-menu'] = 'menu-navigation';
    $variables['attributes']['data-collapsible'] = 'menu-accordion';

    foreach ($variables['items'] as $key => $item) {
      $variables['items'][$key]['attributes']->addClass('bold');

      /** @var \Drupal\Core\Url $url */
      $url = $variables['items'][$key]['url'];
      $url_class = [
        'waves-effect',
        'waves-cyan',
      ];
      if (!empty($item['below'])) {
        $url_class[] = 'collapsible-header';
      }
      $url->setOption('attributes', [
        'class' => $url_class,
      ]);
    }
  }

}
