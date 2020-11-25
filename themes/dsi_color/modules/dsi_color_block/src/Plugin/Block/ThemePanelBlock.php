<?php

namespace Drupal\dsi_color_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ThemePanelBlock' block.
 *
 * @Block(
 *  id = "theme_panel_block",
 *  admin_label = @Translation("Theme panel block"),
 * )
 */
class ThemePanelBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'theme_panel_block';
    $build['#content']['theme_panel_block']['#markup'] = 'Implement ThemePanelBlock.';

    return $build;
  }

}
