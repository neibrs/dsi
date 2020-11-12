<?php

namespace Drupal\dsi_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'FooterBlock' block.
 *
 * @Block(
 *  id = "footer_block",
 *  admin_label = @Translation("Footer block(dsi)"),
 * )
 */
class FooterBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'footer_block';
    $build['footer_block']['#markup'] = 'Implement FooterBlock.';

    return $build;
  }

}
