<?php

namespace Drupal\materialize\Plugin\Preprocess;

/**
 * Pre-processes variables for the "page" theme hook.
 *
 * @ingroup plugins_preprocess
 *
 * @MaterializePreprocess("block")
 */
class Block extends PreprocessBase {

  public function preprocess(array &$variables, $hook, array $info) {
    if ($variables['base_plugin_id'] == 'brand_sidebar_block') {
      $variables['elements']['content']['site_logo_mini']['#uri'] = '/' . $variables['directory'] . '/logo.mini.png';
      $variables['content']['site_logo_mini']['#uri'] = '/' . $variables['directory'] . '/logo.mini.png';
    }
    // 修改tb_megamenu_menu_block
    // TODO
    if ($variables['base_plugin_id'] == 'tb_megamenu_menu_block') {
      // 添加 sitebranding, user_center(login, center)
      $variables['elements']['content']['site_branding_block'] = \Drupal::service('plugin.manager.block')->createInstance('system_branding_block')->build();
      $variables['elements']['content']['user_center_block'] = \Drupal::service('plugin.manager.block')->createInstance('user_center_block')->build();
      $x = 'a';
    }
  }

}
