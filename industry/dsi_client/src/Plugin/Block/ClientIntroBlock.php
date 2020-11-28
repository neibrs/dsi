<?php

namespace Drupal\dsi_client\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'ClientIntroBlock' block.
 *
 * @Block(
 *  id = "client_intro_block",
 *  admin_label = @Translation("Client intro block"),
 * )
 */
class ClientIntroBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'client_intro_block';
    $build['#content']['#markup'] = 'Implement ClientIntroBlock.';

    // add link
    // list

    return $build;
  }

}
