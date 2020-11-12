<?php

namespace Drupal\views_plus\Plugin\views\field;

use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Form\FormState;
use Drupal\views\Plugin\views\field\EntityField;
use Drupal\views\ResultRow;

/**
 * @ViewsField("editable_field")
 */
class EditableField extends EntityField {

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['editable'] = ['default' => FALSE];

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function advancedRender(ResultRow $values) {
    $entity = $this->getEntity($values);
    if (!$this->options['editable'] || !$entity->access('update', \Drupal::currentUser())) {
      return parent::advancedRender($values);
    }

    $display = EntityFormDisplay::collectRenderDisplay($entity, 'default');
    $field = $this->field;
    $widget = $display->getRenderer($field);
    $form = ['#parents' => []];
    $form_state = new FormState();
    $items = $entity->get($field);
    $items->filterEmptyItems();
    $build = $widget->form($items, $form, $form_state);

    if (isset($build['widget'][0]['value'])) {
      $build['widget'][0]['value']['#value'] = $build['widget'][0]['value']['#default_value'];
      $build['widget'][0]['value']['#title_display'] = 'hidden';
      $build['widget'][0]['value']['#description_display'] = 'hidden';
      $build['#process'][] = [$display, 'processForm'];
      $build['widget'][0]['value']['#attributes']['data-update-entity'] = $entity->getEntityTypeId();
      $build['widget'][0]['value']['#attributes']['data-update-field'] = $field;
      $build['widget'][0]['value']['#attributes']['data-update-keys'] = \GuzzleHttp\json_encode([
        'id' => $entity->id(),
      ]);
      $build['widget'][0]['value']['#attributes']['class'] = ['data-update-entity'];
    }
    elseif (isset($build['widget'][0]['target_id'])) {
      // TODO
    }

    $build['widget']['#attributes'] = $build['#attributes'];

    $build['#attached']['library'][] = 'views_plus/editable';

    return \Drupal::service('renderer')->render($build);
  }

}
