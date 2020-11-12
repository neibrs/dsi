<?php

namespace Drupal\entity_filter\Form;

use Drupal\Core\Form\FormStateInterface;

/**
 * Used by route entity_filter.edit_form, which is used by:
 * @see \Drupal\entity_filter\Plugin\Field\FieldFormatter\EntityFilterFormatter
 * @see \Drupal\entity_filter\Plugin\Field\FieldWidget\EntityFilterWidget
 */
class EntityFilterForm extends FiltersFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL, $entity_id = NULL, $field_name = NULL, $target_type = NULL) {
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type_id)->load($entity_id);
    $this->entity = $entity;
    $this->field_name = $field_name;

    if (!$target_type) {
      // Retrieve the target_type
      $field = \Drupal::service('entity_field.manager')->getFieldDefinitions($entity_type_id, $entity->bundle())[$field_name];
      $target_type = $field->getSetting('target_type');
    }

    // 获取 base_table
    $target_type = $this->entityTypeManager->getDefinition($target_type);
    $base_table = $target_type->getDataTable() ?: $target_type->getBaseTable();

    $form = parent::buildForm($form, $form_state, $base_table, $entity->$field_name->value);
    $form['#attached']['library'][] = 'entity_filter/entity_filter_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $entity = $this->entity;
    $field_name = $this->field_name;
    $entity->$field_name->value = $form_state->getValue('filters');
    $entity->save();
  }

}
