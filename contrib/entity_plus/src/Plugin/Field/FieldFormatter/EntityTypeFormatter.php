<?php

namespace Drupal\entity_plus\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;

/**
 * @FieldFormatter(
 *   id = "entity_type",
 *   label = @Translation("Entity type"),
 *   field_types = {
 *     "entity_type",
 *   }
 * )
 */
class EntityTypeFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    $definitions = \Drupal::entityTypeManager()->getDefinitions();
    foreach ($items as $delta => $item) {
      $entity_type_id = $item->value;
      if (isset($definitions[$entity_type_id])) {
        $elements[$delta] = [
          '#markup' => $definitions[$entity_type_id]->getLabel(),
        ];
      }
    }

    return $elements;
  }

}
