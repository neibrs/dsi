<?php

namespace Drupal\dsi_record\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'RecordBlock' block.
 *
 * @Block(
 *  id = "dsi_record_block",
 *  admin_label = @Translation("Record block"),
 * )
 */
class RecordBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'dsi_record_block';
    $build['dsi_record_block']['#markup'] = 'Implement RecordBlock.';

    return $build;
  }

}
