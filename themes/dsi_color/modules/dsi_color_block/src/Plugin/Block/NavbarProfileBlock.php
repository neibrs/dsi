<?php

namespace Drupal\dsi_color_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'NavbarProfileBlock' block.
 *
 * @Block(
 *  id = "dsi_navbar_profile_block",
 *  admin_label = @Translation("Navbar profile block"),
 * )
 */
class NavbarProfileBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'dsi_navbar_profile_block';
     $build['dsi_navbar_profile_block']['#markup'] = 'Implement NavbarProfileBlock.';

    return $build;
  }

}
