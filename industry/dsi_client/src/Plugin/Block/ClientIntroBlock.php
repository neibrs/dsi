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

  public function defaultConfiguration() {
    return [
      'entity_type' => '',
      'entity_id' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'client_intro_block';

    if (empty($this->configuration['entity_type']) || empty($this->configuration['entity_id'])) {
      return $build;
    }
    // add link
    // list
    $client = \Drupal::entityTypeManager()->getStorage($this->configuration['entity_type'])->load($this->configuration['entity_id']);
    $build['#content']['add_link'] = $this->configuration['entity_id'];//$client->toLink('简单修改', 'edit-form', ['dsi_client' => $this->configuration['entity_id']])->toString();//->toRenderable();
    $build['#content']['intro'] = $client->get('summary')->value;

    return $build;
  }

}
