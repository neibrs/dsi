<?php

namespace Drupal\dsi_color_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'NavbarUserBlock' block.
 *
 * @Block(
 *  id = "navbar_user_block",
 *  admin_label = @Translation("Navbar user block"),
 * )
 */
class NavbarUserBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'navbar_user_block';
    $build['navbar_user_block']['#markup'] = 'Implement NavbarUserBlock.';

    return $build;
  }

}
