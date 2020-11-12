<?php

namespace Drupal\dsi_block\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Link;
use Drupal\Core\Url;

/**
 * Provides a 'HeaderTopBlock' block.
 *
 * @Block(
 *  id = "header_top_block",
 *  admin_label = @Translation("Header top block(dsi)"),
 * )
 */
class HeaderTopBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'header_top_block';
    $build['#attributes']['class'][] = 'container';

    $welcomes['yizhan'] = [
      '#type' => 'item',
      '#markup' => '欢迎访问火狐互联一站式平台',
    ];
    $build['welcome'] = [
      '#theme' => 'item_list',
      '#items' => $welcomes,
      '#wrapper_attributes' => [
        'class' => ['user-welcome', 'float-left'],
      ],
    ];

    $items['user_login_or_register'] = [
      '#type' => 'item',
      '#markup' => $this->t('@login || @register', [
        '@login' => Link::fromTextAndUrl('您好, 请登录', Url::fromRoute('user.login', [], ['attributes' => ['class' => ['menu', 'link-menu', 'login-menu']]]))->toString(),
        '@register' => Link::fromTextAndUrl('免费注册', Url::fromRoute('user.register', [], ['attributes' => ['class' => ['menu', 'link-menu', 'login-menu']]]))->toString(),
      ]),
      '#attributes' => [
        'class' => ['user_login_or_register'],
      ],
    ];
    $items['chongzhi'] = [
      '#type' => 'item',
      '#markup' => Link::fromTextAndUrl('充值', Url::fromRoute('<front>', [], ['attributes' => ['class' => ['menu', 'link-menu']]]))->toString(),
    ];
    $items['beian'] = [
      '#type' => 'item',
      '#markup' => Link::fromTextAndUrl('备案', Url::fromRoute('<front>', [], ['attributes' => ['class' => ['menu', 'link-menu']]]))->toString(),
    ];
    $items['bz'] = [
      '#type' => 'item',
      '#markup' => Link::fromTextAndUrl('帮助', Url::fromRoute('<front>', [], ['attributes' => ['class' => ['menu', 'link-menu']]]))->toString(),
    ];

    $build['user_center'] = [
      '#theme' => 'item_list',
      '#items' => $items,
      '#wrapper_attributes' => [
        'class' => ['user-center', 'float-right'],
      ],
    ];

    return $build;
  }

  public function getCacheMaxAge() {
    return 0;
  }

}
