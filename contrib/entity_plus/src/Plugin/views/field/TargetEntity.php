<?php

namespace Drupal\entity_plus\Plugin\views\field;

use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;

/**
 * 将 entity_id 输出为实体名称.
 *
 * @ViewsField("target_entity")
 */
class TargetEntity extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $value = $this->getValue($values);
    $target_entity_type = $values->{$this->aliases['target_entity_type']};

    if (empty($value)) {
      return t('%label is empty', ['%label' => t('Target entity')]);
    }
    elseif (empty($target_entity_type)) {
      return t('%label is empty', ['%label' => t('Target entity type')]);
    }

    $entity = \Drupal::entityTypeManager()->getStorage($target_entity_type)->load($value);

    return $entity ? $entity->toLink()->toString() : t('Entity not found: @label', ['@label' => $value]);
  }

}
