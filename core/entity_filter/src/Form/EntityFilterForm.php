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
  public function buildForm(array $form, FormStateInterface $form_state, $entity_type_id = NULL, $entity_id = NULL, $field_name = NULL) {
    $entity = \Drupal::entityTypeManager()->getStorage($entity_type_id)->load($entity_id);
    $this->entity = $entity;
    $this->field_name = $field_name;

    // Retrieve the target_type
    $field = \Drupal::entityManager()->getFieldDefinitions($entity_type_id, $entity->bundle())[$field_name];
    $target_type = $field->getSetting('target_type');

    $form = parent::buildForm($form, $form_state, $target_type, $entity->$field_name->value);
    $form['#attached']['library'][] = 'entity_filter/entity_filter_form';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildFilters($filters) {
    $build = parent::buildFilters($filters);

    array_unshift($build['#header'], $this->t('Essential'));

    foreach ($filters as $id => $filter) {
      $build[$id] = [
        'essential' => [
          '#type' => 'checkbox',
          '#default_value' => $filter['essential'],
        ],
      ] + $build[$id];
    }

    $build['#attributes']['data-theme'] = 'entity_filter_form';

    return $build;
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
