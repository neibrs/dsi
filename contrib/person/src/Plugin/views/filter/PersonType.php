<?php

namespace Drupal\person\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\Bundle;

/**
 * Filter handler for person type with depth.
 *
 * @ingroup views_filter_handlers
 *
 * @ViewsFilter("person_type")
 */
class PersonType extends Bundle {
  
  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    
    $options['include_children'] = ['default' => FALSE];
    
    return $options;
  }
  
  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    parent::valueForm($form, $form_state);
  
    $form['include_children'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Include children'),
      '#default_value' => $this->options['include_children'],
    ];
  }
  
  /**
   * {@inheritdoc}
   */
  public function query() {
    $person_type_storage = $this->entityTypeManager->getStorage('person_type');

    $children = [];
    foreach ($this->value as $key => $val) {
      $person_type = $person_type_storage->load($val);
      if (empty($person_type)) {
        continue;
      }
      if (isset($this->options['include_children']) && $this->options['include_children']) {
        $children[$key] = $person_type->loadAllChildren();
      }
    }
    $sub_types = [];
    foreach ($children as $id => $types) {
      foreach ($types as $k => $v) {
        $sub_types[$k] = $k;
      }
    }
    $this->value = $this->value + $sub_types;

    parent::query();
  }
}