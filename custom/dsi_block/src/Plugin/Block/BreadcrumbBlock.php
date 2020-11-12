<?php

namespace Drupal\dsi_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'BreadcrumbBlock' block.
 *
 * @Block(
 *  id = "dsi_breadcrumb_block",
 *  admin_label = @Translation("Breadcrumb block"),
 * )
 */
class BreadcrumbBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'dsi_breadcrumb_block';
    $page_title_block = \Drupal::service('plugin.manager.block')->createInstance('page_title_block');

    $build['breadcrumb_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'col s10 m6 l6',
        ],
      ],
    ];
    //    $build['page_title'] = ->build();
    $build['breadcrumb_container']['breadcrumb'] = \Drupal::service('plugin.manager.block')->createInstance('system_breadcrumb_block')->build();

    $build['breadcrumb_actions'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => [
          'col s2 m6 l6',
        ],
      ],
    ];
    $build['breadcrumb_actions']['local_actions'] = \Drupal::service('plugin.manager.block')->createInstance('local_actions_block')->build();

    return $build;
  }

  public function getCacheMaxAge() {
    return 0;
  }

}
