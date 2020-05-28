<?php

namespace Drupal\small_title\Element;

use Drupal\Core\Render\Element\RenderElement;

/**
 * @RenderElement("page_small_title")
 */
class PageSmallTitle extends RenderElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return [
      '#theme' => 'page_small_title',
      // The page title: either a string for plain titles or a render array for
      // formatted titles.
      '#title' => NULL,
      '#small_title' => NULL,
    ];
  }

}
