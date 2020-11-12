<?php

namespace Drupal\person\Plugin\Field\FieldFormatter;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\Plugin\Field\FieldFormatter\EntityReferenceFormatterBase;

/**
 * @FieldFormatter(
 *   id = "user_person",
 *   label = @Translation("User person"),
 *   field_types = {
 *     "entity_reference"
 *   }
 * )
 */
class UserPersonFormatter extends EntityReferenceFormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($this->getEntitiesToView($items, $langcode) as $delta => $entity) {
      $label = '';
      if ($person = $entity->person->entity) {
        $label = $person->label();
      }

      $elements[$delta] = [
        '#plain_text' => $label,
      ];

      $elements[$delta]['#cache']['tags'] = $entity->getCacheTags();
      if ($person) {
        $elements[$delta]['#cache']['tags'] = Cache::mergeTags($elements[$delta]['#cache']['tags'], $person->getCacheTags());
      }
    }

    return $elements;
  }
}
