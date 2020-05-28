<?php

namespace Drupal\layout_template\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

class LayoutTemplateSwitchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'layout_template_switch';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $type = NULL, $related_config = NULL) {
    $layout_template_storage = \Drupal::entityTypeManager()->getStorage('layout_template');
    $query = $layout_template_storage->getQuery();
    $or = $query->orConditionGroup()
      ->condition('is_public', TRUE)
      ->condition('user_id', \Drupal::currentUser()->id());
    $query->condition($or);

    if ($type != NULL && $related_config != NULL) {
      $and = $query->andConditionGroup()
        ->condition('type', $type)
        ->condition('related_config', $related_config);
      $query->condition($and);
    }
    $ids = $query->execute();
    $entities = $layout_template_storage->loadMultiple($ids);
    $options = array_map(function ($layout_template) {
      return $layout_template->label();
    }, $entities);
    $form['layout_template'] = [
      '#title' => $this->t('Layout template'),
      '#type' => 'select',
      '#options' => $options,
      '#attributes' => [
        'class' => ['select-submit'],
      ],
    ];
    /** @var \Drupal\layout_template\LayoutTemplateManagerInterface $layout_template_manager */
    $layout_template_manager = \Drupal::service('layout_template.manager');
    if ($layout_template = $layout_template_manager->currentLayoutTemplate($type, $related_config)) {
      $form['layout_template']['#default_value'] = $layout_template->id();
    }
    else {
      $ids = array_keys($options);
      if ($layout_template_id = reset($ids)) {
        $layout_template = $layout_template_storage->load($layout_template_id);
      }
    }

    $current_user = \Drupal::currentUser();

    $links = [];
    if ($current_user->hasPermission('administer own layout templates')) {
      $links['add'] = [
        'title' => $this->t('Add'),
        'url' => $this->ensureDestination(Url::fromRoute('entity.layout_template.add_form', [
          'layout_template_type' => $type,
          'related_config' => $related_config,
        ])),
      ];
    }
    if ($layout_template) {
      if ($layout_template->access('update', $current_user)) {
        $links['edit'] = [
          'title' => $this->t('Edit'),
          'url' => $this->ensureDestination(Url::fromRoute('entity.layout_template.edit_form', [
            'layout_template' => $layout_template->id(),
          ])),
        ];
      }
      if ($layout_template->access('delete', $current_user)) {
        $links['delete'] = [
          'title' => $this->t('Delete'),
          'url' => $this->ensureDestination(Url::fromRoute('entity.layout_template.delete_form', [
            'layout_template' => $layout_template->id(),
          ])),
        ];
      }
    }
    if (!empty($links)) {
      $form['operations'] = [
        '#theme' => 'links',
        '#links' => $links,
      ];
    }

    $form['actions']['#type'] = 'actions';
    $form['actions']['#attributes']['class'] = ['hidden'];
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Switch'),
      '#button_type' => 'primary',
    ];

    // TODO: Fix: There has no class for form output.
    $form['#attributes']['class'][] = 'container-inline';
    $form['#attached']['library'][] = 'layout_template/switch_form';

    return $form;
  }

  protected function ensureDestination(Url $url) {
    // \Drupal::destination() will return destination from request query.
    // return $url->mergeOptions(['query' => \Drupal::destination()->getAsArray()]);
    return $url->mergeOptions(['query' => ['destination' => Url::fromRoute('<current>')->toString()]]);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $route_match = \Drupal::routeMatch();
    $route_name = $route_match->getRouteName();
    $route_parameters = $route_match->getRawParameters()->getIterator()->getArrayCopy();
    $layout_template = $form_state->getValue('layout_template');

    $form_state->setRedirect($route_name, $route_parameters, [
      'query' => ['layout_template' => $layout_template],
    ]);
  }

}
