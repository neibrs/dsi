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

    // 获取 views filters 基表的实体类型
    $field_definition = $items->getFieldDefinition();
    $target_type = $field_definition->getSetting('target_type');
    if (empty($target_type)) {
      $target_type_field = $field_definition->getSetting('target_type_field');
      $target_type = $entity->$target_type_field->value;
    }

    $field_name = $items->getName();
    $elements[0] = \Drupal::service('entity_filter.manager')
      ->buildFiltersDisplayForm($entity->$field_name->value, Url::fromRoute($this->getFiltersFormRouteName(), [
        'entity_type_id' => $entity->getEntityTypeId(),
        'entity_id' => $entity->id(),
        'field_name' => $field_definition->getName(), // 条件设置结果保存在这个字段.
        'target_type' => $target_type,                // 条件设置的基表.
      ], [
        'query' => \Drupal::destination()->getAsArray(),
      ]));
    unset($elements[0]['label']);

    return $elements;
  }

  /**
   * 方便派生类修改路由名称.
   *
   * @see \Drupal\job\Plugin\Field\FieldFormatter\RequirementsFormatter
   *
   * @return string
   */
  protected function getFiltersFormRouteName() {
    return 'entity_filter.edit_form';
  }

}
