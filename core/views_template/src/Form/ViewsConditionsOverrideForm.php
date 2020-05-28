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
  public function buildForm(array $form, FormStateInterface $form_state, ViewEntityInterface $view = NULL) {
    $this->view = $view;

    $views_data = \Drupal::service('views.views_data')->get($view->get('base_table'));

    $view_override = \Drupal::service('views_template.manager')->getViewOverride($view);
    $this->view_override = $view_override;

    if (isset($views_data['table']['entity type'])) {
      $base_table = $views_data['table']['entity type'];
    }
    else {
      $base_table = $view->get('base_table');
    }

    return parent::buildForm($form, $form_state, $base_table, $view_override['filters']);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $view_override = $this->view_override;
    $view_override['filters'] = $form_state->getValue('filters');

    \Drupal::service('views_template.manager')->setViewOverride($this->view, $view_override);

    // Clear cache
    /** @var \Drupal\views\ViewEntityInterface $view */
    $view = $this->view;
    Cache::invalidateTags($view->getCacheTags());
  }

}
