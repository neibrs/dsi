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
  public function buildForm(array $form, FormStateInterface $form_state, $view_id = NULL) {
    if (!$view = \Drupal::entityTypeManager()->getStorage('view')->load($view_id)) {
      throw new \Exception('Could not load view: ' . $view_id);
    }
  
    $form['actions']['#type'] = 'actions';
  
    $form['actions']['conditions_override'] = [
      '#title' => $this->t('Conditions setting'),
      '#type' => 'link',
      '#url' => \Drupal\Core\Url::fromRoute('views_template.conditions_override_form', ['view' => $view_id], [
        'query' => \Drupal::destination()->getAsArray(),
      ]),
      '#attributes' => [
        'class' => ['use-ajax', 'btn', 'btn-primary', 'btn-sm'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => '{"width":"60%"}',
      ],
    ];
  
    $form['actions']['fields_override'] = [
      '#title' => $this->t('Fields setting'),
      '#type' => 'link',
      '#url' => \Drupal\Core\Url::fromRoute('views_template.fields_override_form', ['view' => $view_id], [
        'query' => \Drupal::destination()->getAsArray(),
      ]),
      '#attributes' => [
        'class' => ['use-ajax', 'btn', 'btn-primary', 'btn-sm'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => '{"width":"60%"}',
      ],
    ];
  
    $form['actions']['save_as'] = [
      '#title' => $this->t('View template save as'),
      '#type' => 'link',
      '#url' => \Drupal\Core\Url::fromRoute('entity.view_template.add_form', ['view' => $view_id], [
        'query' => \Drupal::destination()->getAsArray(),
      ]),
      '#attributes' => [
        'class' => ['btn', 'btn-primary', 'btn-sm'],
      ],
    ];
  
    $storage = \Drupal::entityTypeManager()->getStorage('view_template');
    $query = $storage->getQuery();
    $query->condition('view_id', $view_id);
    $query->condition($query->orConditionGroup()
      ->condition('is_public', TRUE)
      ->condition('user_id', \Drupal::currentUser()->id())
    );
    $entities = $storage->loadMultiple($query->execute());
    $options['none'] = $this->t('-Select template-');
    $options += array_map(function ($entity) {
      /** @var \Drupal\views_template\Entity\ViewTemplateInterface $entity */
      return $entity->label() . ($entity->getIsPublic() ? $this->t('[Public]') : '');
    }, $entities);
  
    $form['view_template'] = [
      '#type' => 'select',
      '#options' => $options,
      '#attributes' => ['class' => ['select-submit']],
      '#attached' => ['library' => ['eabax_core/select_submit']],
      '#weight' => 100,
    ];
  
    if ($view_template_id = \Drupal::service('views_template.manager')->getViewTemplate($view_id)) {
      $form['view_template']['#default_value'] = $view_template_id;
    }
  
    $form['actions']['switch'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
      '#button_type' => 'primary',
      '#submit' => ['::switchSubmit'],
      '#attributes' => ['class' => ['hide']],
    ];
  
    $form['#cache']['contexts'][] = 'user'; // 每个用户的方案可能是不一样的。
    $form['#cache']['contexts'][] = 'url';  // 每个列表的方案是不一样的。

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