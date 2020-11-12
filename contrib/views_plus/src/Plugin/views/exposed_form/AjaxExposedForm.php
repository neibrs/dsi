<?php

namespace Drupal\views_plus\Plugin\views\exposed_form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\exposed_form\ExposedFormPluginBase;

/**
 * @ViewsExposedForm(
 *   id = "ajax",
 *   title = @Translation("Ajax"),
 *   help = @Translation("Ajax exposed form")
 * )
 */
class AjaxExposedForm extends ExposedFormPluginBase {

  /**
   * {@inheritdoc}
   */
  public function exposedFormAlter(&$form, FormStateInterface $form_state) {
    parent::exposedFormAlter($form, $form_state);

    $form['actions']['submit']['#attributes']['class'][] = 'ajax-submit';
    $view_id = $this->view->id();
    $form['actions']['submit']['#attributes']['rel'] = '.view-id-' . $view_id;
    $form['#attached']['library'][] = 'views_plus/ajax_exposed_form';
  }

}
