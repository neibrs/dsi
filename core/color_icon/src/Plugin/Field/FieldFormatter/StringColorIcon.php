<?php

namespace Drupal\color_icon\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * @FieldFormatter(
 *   id = "string_color_icon",
 *   label = @Translation("Color icon"),
 *   field_types = {
 *     "string",
 *   },
 * )
 */
class StringColorIcon extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];
    foreach ($items as $delta => $item) {
      $elements[$delta] = [
        '#markup' => '<span class="color-square" data-fill-color="' . $item->value . '"></span>',
        '#attached' => ['library' => ['color_icon/color_icon']],
      ];
    }

    return $elements;
  }

}
