<?php

namespace Drupal\font_awesome\Render\Element;

use Drupal\Core\Render\Element\Link;

/**
 * @RenderElement("font_awesome_link")
 */
class FontAwesomeLink extends Link {

  /**
   * {@inheritdoc}
   */
  public static function preRenderLink($element) {
    $element = parent::preRenderLink($element);

    if (!empty($element['#icon'])) {
      // TODO
    }

    return $element;
  }

}
