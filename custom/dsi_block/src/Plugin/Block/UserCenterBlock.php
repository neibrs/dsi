<?php

namespace Drupal\dsi_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'UserCenterBlock' block.
 *
 * @Block(
 *  id = "user_center_block",
 *  admin_label = @Translation("User center block"),
 * )
 */
class UserCenterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'user_center_block';
    $build['user_center_block']['#markup'] = 'Implement UserCenterBlock.';

    return $build;
  }

}
