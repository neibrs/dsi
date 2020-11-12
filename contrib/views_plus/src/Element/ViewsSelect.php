<?php

namespace Drupal\views_plus\Element;

use Drupal\Core\Render\Element\FormElement;

/**
 * @FormElement("views_select")
 */
class ViewsSelect extends FormElement {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#pre_render' => [
        [$class, 'preRender'],
      ],
      '#name' => NULL,
      '#display_id' => 'default',
      '#arguments' => [],
      '#input' => TRUE,
      '#theme_wrappers' => ['form_element'],
    ];
  }

  /**
   * Element pre render callback.
   */
  public static function preRender($element) {
    $element['input'] = [
      '#type' => 'hidden',
    ];
    if (isset($element['#default_value'])) {
      $element['input']['#value'] = $element['#default_value'];
    }

    $element['views'] = [
      '#type' => 'view',
      '#name' => $element['#name'],
      '#display_id' => $element['#display_id'],
      '#arguments' => $element['#arguments'],
    ];

    $element['#attached']['library'][] = 'views_plus/views_select';

    return $element;
  }

}
