<?php

namespace Drupal\entity_plus\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * 将 entity_type_id 输出为实体类型名称.
 *
 * @ViewsField("target_entity_type")
 */
class TargetEntityType extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $value = $this->getValue($values);

    if (empty($value)) {
      return '';
    }

    $definition = \Drupal::entityTypeManager()->getDefinition($value);

    return $definition ? $definition->getLabel() : t('Entity type not found: @label', ['@label' => $value]);
  }

}
