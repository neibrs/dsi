<?php

namespace Drupal\views_template\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class ViewTemplateSwitchForm extends FormBase {
  
  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'view_template_switch_form';
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $view_id = \Drupal::routeMatch()->getRouteObject()->getDefault('view_id');
    // 如果当前显示的页面是视图页面, $view_id就不为空.
    if (!$view_id) {
      return;
    }
    if (!$view = \Drupal::entityTypeManager()->getStorage('view')->load($view_id)) {
      throw new \Exception('Could not load view: ' . $view_id);
    }
    
    $form['view_container'] = [
      '#type' => 'container',
      '#attributes' => [
        'class' => 'container-inline float-right',
      ],
    ];
  
    $form['view_container']['actions']['conditions_override'] = [
      '#title' => $this->t('Conditions setting'),
      '#type' => 'link',
      '#url' => \Drupal\Core\Url::fromRoute('views_template.conditions_override_form', ['view' => $view_id], [
        'query' => \Drupal::destination()->getAsArray(),
      ]),
      '#attributes' => [
        'class' => ['use-ajax', 'btn', 'btn-primary', 'btn-sm'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => '{"width":"80%"}',
      ],
    ];
  
    $form['view_container']['actions']['fields_override'] = [
      '#title' => $this->t('Fields setting'),
      '#type' => 'link',
      '#url' => \Drupal\Core\Url::fromRoute('views_template.fields_override_form', ['view' => $view_id], [
        'query' => \Drupal::destination()->getAsArray(),
      ]),
      '#attributes' => [
        'class' => ['use-ajax', 'btn', 'btn-primary', 'btn-sm'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => '{"width":"80%"}',
      ],
    ];
  
    $form['view_container']['actions']['save_as'] = [
      '#title' => $this->t('View template save as'),
      '#type' => 'link',
      '#url' => \Drupal\Core\Url::fromRoute('views_template.save_as_form', ['view' => $view_id], [
        'query' => \Drupal::destination()->getAsArray(),
      ]),
      '#attributes' => [
        'class' => ['btn', 'btn-primary', 'btn-sm'],
      ],
    ];
  
    $entities = \Drupal::entityTypeManager()->getStorage('view_template')
      ->loadByProperties([
        'view_id' => $view_id,
      ]);
    $options = array_map(function ($entity) {
      return $entity->label();
    }, $entities);
    $form['view_container']['actions']['view_template'] = [
      '#type' => 'select',
      '#options' => $options,
      '#attributes' => ['class' => ['select-submit']],
      '#attached' => ['library' => ['eabax_core/select_submit']],
    ];
  
    if ($view_template_id = \Drupal::service('views_template.manager')->getViewTemplate($view_id)) {
      $form['view_container']['view_template']['#default_value'] = $view_template_id;
    }
  
    $form['view_container']['actions']['#type'] = 'actions';
    $form['view_container']['actions']['switch'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#submit' => ['::switchSubmit'],
      '#attributes' => ['class' => ['hide']],
    ];
    
    return $form;
  }
  
  public function switchSubmit(array &$form, FormStateInterface $form_state) {
    $view_id = \Drupal::routeMatch()->getRouteObject()->getDefault('view_id');
    $template = $form_state->getValue('view_template');
    \Drupal::service('views_template.manager')->setViewTemplate($view_id, $template);
  }
  
  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
  }
}