<?php

namespace Drupal\eabax_core\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * Plugin implementation of the 'percent_bar' formatter.
 *
 * @FieldFormatter(
 *   id = "percent_bar",
 *   label = @Translation("Percent Bar"),
 *   field_types = {
 *     "integer"
 *   }
 * )
 */
class PercentBarFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {

    // Implement settings summary.
    $summary[] = $this->t('This field has percent bar.');

    return $summary;
  }

  /**
   * Builds a renderable array for a field value.
   *
   * @param \Drupal\Core\Field\FieldItemListInterface $items
   *   The field values to be rendered.
   * @param string $langcode
   *   The language that should be used to render the field.
   *
   * @return array
   *   A renderable array for $items, as an array of child elements keyed by
   *   consecutive numeric indexes starting from 0.
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $percent = $items->getValue()[0]['value'];
    // Return everything in an array for theming.
    return [
      '#theme' => 'percent_bar',
      '#percent' => !empty($percent) ? $percent : 0,
    ];
  }

}
