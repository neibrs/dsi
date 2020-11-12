<?php

namespace Drupal\views_template\Form;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Form\FormStateInterface;
use Drupal\entity_filter\Form\FiltersFormBase;
use Drupal\views\ViewEntityInterface;

class ViewsConditionsOverrideForm extends FiltersFormBase {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $base_table = NULL, $filters = NULL, ViewEntityInterface $view = NULL) {
    $this->view = $view;

    $view_override = \Drupal::service('views_template.manager')->getViewOverride($view);
    $this->view_override = $view_override;

    $base_table = $view->get('base_table');

    $form = parent::buildForm($form, $form_state, $base_table, isset($view_override['filters']) ? $view_override['filters'] : NULL);
    
    $form['actions']['submit']['#value'] = '保存条件设置';
    
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $view_override = $this->view_override;
    $view_override['filters'] = $form_state->getValue('filters');

    \Drupal::service('views_template.manager')->setViewOverride($this->view, $view_override);
  }

}
