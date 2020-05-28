<?php

namespace Drupal\small_title\Plugin\Block;

use Drupal\Core\Block\Plugin\Block\PageTitleBlock;

/**
 * Used to replace page_title_block.
 */
class SmallTitleBlock extends PageTitleBlock implements SmallTitleBlockPluginInterface {

  protected $small_title = '';

  public function setSmallTitle($title) {
    $this->small_title = $title;
    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    return [
      '#type' => 'page_small_title',
      '#title' => $this->title,
      '#small_title' => $this->small_title,
    ];
  }

}
