<?php

namespace Drupal\entity_filter\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Plugin implementation of the 'entity_filter' widget.
 *
 * @FieldWidget(
 *   id = "entity_filter",
 *   label = @Translation("Entity filter"),
 *   field_types = {
 *     "entity_filter"
 *   }
 * )
 */
class EntityFilterWidget extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $entity = $items->getEntity();
    $element = \Drupal::service('entity_filter.manager')
      ->buildFiltersDisplayForm($this->configuration['filters'], Url::fromRoute('entity_filter.edit_form', [
        'entity_type_id' => $entity->getEntityTypeId(),
        'entity_id' => $entity->id(),
        'field_name' => $items->getFieldDefinition()->getName(),
      ], [
        'query' => \Drupal::destination()->getAsArray(),
      ]));

    return $element;
  }
}
