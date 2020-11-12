<?php

namespace Drupal\views_template\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a 'View template' block.
 *
 * @Block(
 *  id = "view_template_block",
 *  admin_label = @Translation("View template block"),
 * )
 */
class ViewTemplateBlock extends BlockBase {
  
  /**
   * {@inheritdoc}
   */
  public function build() {
    $view_id = \Drupal::routeMatch()->getRouteObject()->getDefault('view_id');
    // 如果当前显示的页面是视图页面, $view_id就不为空.
    if ($view_id) {
      $form = \Drupal::formBuilder()->getForm('\Drupal\views_template\Form\ViewTemplateSwitchForm', $view_id);
      return $form;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getCacheContexts() {
    // 不同的视图类表需要不同的列表方案切换区块。
    return Cache::mergeContexts(parent::getCacheContexts(), ['url', 'user']);
  }

}
