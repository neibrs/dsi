<?php

namespace Drupal\views_template\Form;

use Drupal\Component\Utility\SortArray;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\views\ViewEntityInterface;
use Drupal\views\ViewsData;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ViewsFieldsOverrideForm extends FormBase {

  /**
   * @var \Drupal\views\ViewsData
   */
  protected $viewsData;

  public function __construct(ViewsData $views_data) {
    $this->viewsData = $views_data;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('views.views_data')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'views_fields_override_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, ViewEntityInterface $view = NULL) {
    $this->view = $view;

    $view_override = \Drupal::service('views_template.manager')->getViewOverride($view);
    $this->view_override = $view_override;

    $form['selector'] = $this->buildSelector($view);

    $form['override'] = $this->buildOverride($view_override['fields']);
    $form['override']['#prefix'] = '<div id="override-wrapper">';
    $form['override']['#suffix'] = '</div>';

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save configuration'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  protected function buildSelector(ViewEntityInterface $view) {
    $build = [
      '#theme' => 'tree',
      '#items' => [],
    ];

    $data = $this->viewsData->get($view->get('base_table'));
    foreach ($data as $key => $value) {
      $title = '';
      if (isset($value['title'])) {
        $title = $value['title'];
      }
      if (isset($value['field'])) {
        if (isset($value['field']['title'])) {
          $title = $value['field']['title'];
        }
        if (!empty($title)) {
          $build['#items'][$key] = [
            'title' => $title,
          ];
        }
      }
      if (isset($value['relationship'])) {
        if (isset($value['relationship']['title'])) {
          $title = $value['relationship']['title'];
        }
        if (!empty($title)) {
          $build['#items']['relationship.' . $key] = [
            'title' => $title,
            'below' => [],
            'is_expanded' => FALSE,
            'attributes' => new Attribute(),
          ];
        }
      }
    }

    return $build;
  }

  protected function buildOverride($fields) {
    $build = [
      '#type' => 'table',
      '#header' => [
        'field' => $this->t('Field'),
        'operations' => $this->t('Operations'),
        'weight' => $this->t('Weight'),
      ],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'weight',
        ],
      ],
    ];

    $weight = 0;
    foreach ($fields as $id => $field) {
      $build[$id]['#attributes']['class'][] = 'draggable';
      $build[$id]['#weight'] = $weight;

      $build[$id]['label'] = [
        '#type' => 'textfield',
        '#default_value' => $field['label'],
        '#size' => 16,
      ];

      $build[$id]['operations'] = [
        '#type' => 'link',
        '#title' => $this->t('Delete'),
        '#url' => Url::fromRoute('<front>'),
        '#ajax' => [
          'callback' => '::deleteField',
          'wrapper' => 'override-wrapper',
        ],
      ];

      $build[$id]['weight'] = [
        '#type' => 'weight',
        '#default_value' => $weight,
        '#attributes' => ['class' => ['weight']],
      ];

      $weight ++;
    }

    return $build;
  }

  public function deleteField($form, FormStateInterface $form_state) {
    return $form['override'];
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $view_override = $this->view_override;

    $fields = [];
    foreach ($form_state->getValue('override') as $key => $value) {
      $fields[$key] = $view_override['fields'][$key];
      $fields[$key]['label'] = $value['label'];
    }
    $view_override['fields'] = $fields;

    \Drupal::service('views_template.manager')->setViewOverride($this->view, $view_override);
  }

}
