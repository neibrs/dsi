<?php

namespace Drupal\dsi_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'UserNavbarRightBlock' block.
 *
 * @Block(
 *  id = "user_navbar_right_block",
 *  admin_label = @Translation("User navbar right block"),
 * )
 */
class UserNavbarRightBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'user_navbar_right_block';
    $build['user_navbar_right_block']['#markup'] = 'Implement UserNavbarRightBlock.';
    // TODO
    // 1. language select block
    // 2. fullscreen
    // 3. tips
    // 4. user profile
    // 5. side right

    return $build;
  }

}
