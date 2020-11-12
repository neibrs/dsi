<?php

namespace Drupal\dsi_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'HeaderSearchBlock' block.
 *
 * @Block(
 *  id = "header_search_block",
 *  admin_label = @Translation("Header search block"),
 * )
 */
class HeaderSearchBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'header_search_block';
    $build['header_search_block']['#markup'] = 'Implement HeaderSearchBlock.';

    return $build;
  }

}
