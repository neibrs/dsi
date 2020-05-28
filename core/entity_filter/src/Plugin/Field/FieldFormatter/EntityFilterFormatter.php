<?php

namespace Drupal\entity_filter\Plugin\Field\FieldFormatter;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'entity_filter' formatter.
 *
 * @FieldFormatter(
 *   id = "entity_filter",
 *   label = @Translation("Entity filter"),
 *   field_types = {
 *     "entity_filter"
 *   }
 * )
 */
class EntityFilterFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    $entity = $items->getEntity();
    $field_name = $items->getName();
    $elements[0] = \Drupal::service('entity_filter.manager')
      ->buildFiltersDisplayForm($entity->$field_name->value, Url::fromRoute('entity_filter.edit_form', [
        'entity_type_id' => $entity->getEntityTypeId(),
        'entity_id' => $entity->id(),
        'field_name' => $items->getFieldDefinition()->getName(),
      ], [
        'query' => \Drupal::destination()->getAsArray(),
      ]));
    unset($elements[0]['label']);

    return $elements;
  }

}
