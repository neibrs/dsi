<?php

namespace Drupal\dsi_client\Plugin\Block;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;

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
    $client = \Drupal::entityTypeManager()->getStorage($this->configuration['entity_type'])->load($this->configuration['entity_id']);
    $build['#content']['edit_link'] = [
      '#type' => 'link',
      '#title' => $this->t('Edit'),
      '#url' => Url::fromRoute('entity.dsi_client.edit_form', [
        'dsi_client' => $this->configuration['entity_id'],
      ]
      ),
      '#options' => ['attributes' => [
        'class' => ['use-ajax'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode([
          'width' => 700,
        ]),
      ]]];
    $build['#content']['intro'] = $client->get('summary')->value;

    return $build;
  }
  
  /**
   * {@inheritDoc}
   */
  public function getCacheTags() {
    return Cache::mergeTags(parent::getCacheTags(), ['dsi_client_list']);
  }
}
