<?php

namespace Drupal\entity_plus;

use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Field\EntityReferenceFieldItemList;
use Drupal\Core\Messenger\MessengerTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\StringTranslation\TranslatableMarkup;

class EntityPlusManager implements EntityPlusManagerInterface {

  use MessengerTrait;
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  public function calculateFormula($formula = NULL, array $context) {
    if (!$formula) {
      return;
    }

    \Drupal::moduleHandler()->alter('calculate_formula', $formula, $context);

    // 获得翻译后的实体和字段名称.
    $entities_label = [];
    $fields_label = [];
    /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager */
    $entity_field_manager = \Drupal::service('entity_field.manager');
    $translation_manager = \Drupal::translation();
    foreach ($context as $key => $value) {
      if ($key != 'default_entity') {
        $entity_type = \Drupal::entityTypeManager()->getDefinition($key);
        $entity_label = $entity_type->getLabel();
        if ($entity_label instanceof TranslatableMarkup) {
          $entity_label = $translation_manager->translateString($entity_label);
        }
        $entities_label[$key] = $entity_label;

        $fields = $entity_field_manager->getFieldDefinitions($key, $context[$key]->bundle());
        foreach ($fields as $id => $field_definition) {
          $field_label = $field_definition->getLabel();
          if ($field_label instanceof TranslatableMarkup) {
            $field_label = $translation_manager->translateString($field_label);
          }
          $fields_label[$key][$id] = $field_label;
        }
      }
    }

    // Matches
    preg_match_all('/\{([^\{\}]+)\}/x', $formula, $matches_items);
    foreach ($matches_items[1] as $key => $entity_field) {
      $entity_field = explode('.', $entity_field);
      if (count($entity_field) == 1) {
        $entity_type_id = $context['default_entity'];
        $field_name = trim($entity_field[0]);
      }
      else {
        list($entity_type_id, $field_name) = $entity_field;
        $entity_type_id = trim($entity_type_id);
        $field_name = trim($field_name);
      }

      // 检查公式是否含有与上下文无关的实体和字段,即非法公式.
      if (!in_array($entity_type_id, $entities_label) && !array_key_exists($entity_type_id, $entities_label)) {
        $this->messenger()->addError($this->t('Illegal formula : The context does not contain @entity entity.', [
          '@entity' => $entity_type_id,
        ]));
        return;
      }
      if (array_search($entity_type_id, $entities_label)) {
        $entity_type_id = array_search($entity_type_id, $entities_label);
      }
      if (!in_array($field_name, $fields_label[$entity_type_id]) && !array_key_exists($field_name, $fields_label[$entity_type_id])) {
        $this->messenger()->addError($this->t('Illegal formula : @entity does not have @field field.', [
          '@entity' => $entity_type_id,
          '@field' => $field_name,
        ]));
        return;
      }
      if (array_search($field_name, $fields_label[$entity_type_id])) {
        $field_name = array_search($field_name, $fields_label[$entity_type_id]);
      }

      $entity = $context[$entity_type_id];
      $field_item_list = $entity->get($field_name);
      if ($field_item_list instanceof EntityReferenceFieldItemList) {
        if ($target_entity = $field_item_list->entity) {
          $label_key = $target_entity->getEntityType()->getKey('label');
          $value = $target_entity->get($label_key)->value;
        }
        else {
          $value = '';
        }
      }
      else {
        $value = $field_item_list->value;
      }

      $formula = str_replace($matches_items[0][$key], $value, $formula);
    }

    // Find out the redundant fields in the formula.
    foreach ($matches_items[0] as $id => $key) {
      if (strstr($matches_items[0][$id], $formula)) {
        $this->messenger()->addError($this->t('Illegal formula : The entity or field of @item in the formula does not match.', [
          '@item' => $matches_items[0][$id],
        ]));
        return;
      }
    }

    try {
      $target_value = NULL;
      eval('$target_value = ' . $formula . ';');
      return $target_value;
    }
    catch (\Throwable $e) {
      $this->messenger()->addError($this->t('Formula error : @error', [
        '@error' => $e->getMessage(),
      ]));
    }

    return;
  }

  /**
   * {@inheritdoc}
   */
  public function addEffectiveDatesCondition(QueryInterface $query, $start_date = NULL, $end_date = NULL, $prefix = NULL) {
    $start_field = 'effective_dates.value';
    $end_field = 'effective_dates.end_value';
    if ($prefix) {
      $start_field = $prefix . '.' . $start_field;
      $end_field = $prefix . '.' . $end_field;
    }
    if (!empty($start_date)) {
      $query->condition($query->orConditionGroup()
        ->condition($end_field, NULL, 'IS NULL')
        ->condition($end_field, $start_date, '>')
      );
    }
    if (!empty($end_date)) {
      $query->condition($query->orConditionGroup()
        ->condition($start_field, NULL, 'IS NULL')
        ->condition($start_field, $end_date, '<=')
      );
    }
  }

}
