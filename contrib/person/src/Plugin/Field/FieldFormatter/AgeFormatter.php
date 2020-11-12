<?php

namespace Drupal\person\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * @FieldFormatter(
 *   id = "age",
 *   label = @Translation("Age"),
 *   field_types = {
 *     "datetime"
 *   }
 * )
 */
class AgeFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      if ($item->date) {
        /** @var \Drupal\Core\Datetime\DrupalDateTime $date */
        $date = $item->date;
        $elements[$delta] = [
          '#markup' => $date->diff(new \DateTime())->y,
        ];
      }
    }

    return $elements;
  }

}
