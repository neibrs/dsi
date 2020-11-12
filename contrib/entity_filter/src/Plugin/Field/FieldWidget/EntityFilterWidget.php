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
    
    // 新增时没有实体 ID ，不能设置 entity filter.
    if (!$entity->isNew()) {
      $filters = $items->get($delta)->value;
      $element = \Drupal::service('entity_filter.manager')
        ->buildFiltersDisplayForm($filters, Url::fromRoute($this->getFiltersFormRouteName(), [
          'entity_type_id' => $entity->getEntityTypeId(),
          'entity_id' => $entity->id(),
          'field_name' => $items->getFieldDefinition()->getName(),
          'target_type' => $items->get($delta)->getTargetType(),
        ], [
          'query' => \Drupal::destination()->getAsArray(),
        ]));
    }
    return $element;
  }

  /**
   * 方便派生类修改路由名称.
   *
   * @see \Drupal\job\Plugin\Field\FieldWidget\RequirementsWidget
   *
   * @return string
   */
  protected function getFiltersFormRouteName() {
    return 'entity_filter.edit_form';
  }

}
