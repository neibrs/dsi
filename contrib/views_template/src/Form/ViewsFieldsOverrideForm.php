<?php

namespace Drupal\views_template\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormInterface;
use Drupal\Core\Form\FormStateInterface;
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
    if (!isset($view_override['fields'])) {
      $view_override['fields'] = $view->get('display')['default']['display_options']['fields'];
    }
    $this->view_override = $view_override;

    $form['#attached']['library'][] = 'views_template/views_fields_override_form';
  
    $form['selector'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'selector',
      ],
    ];

    // Add option columns
    list($controls, $fields) = $this->buildSelector($form, $view);
    $form['selector']['controls'] = $controls;
    $form['selector']['fields'] = $fields;
    
    $form['override']['options'] = $this->buildFieldOptions();
  
    $fields = $view_override['fields'];
    
    // 处理 AJAX 提交.
    if ($form_state->getValue('op') == '保存') {
      $fields = [];
      $fields_ids = [];
      if ($fields = \Drupal::request()->get('fields')) {
        foreach ($fields as $id => $field) {
          $field = Json::decode($field['options']);
          $fields[$id] = $field;
    
          $fields_ids[] = $id;
        }
      }
  
      $field = $form_state->getUserInput()['options'];
  
      //生成 field 的 id.
      $field_id = NULL;
      if (isset($field['relationship'])) {
        $field_id = $field['relationship'] . '_';
      }
      $field_id = $field_id . $field['field'];
      $id = $field_id;
      foreach ($fields_ids as $fields_id) {
        if ($id == $fields_id) {
          $id = $field_id . '_' . mt_rand();
        }
      }
      $field['id'] = $id;
      
      $fields[$id] = $field;
    }
  
    $form['override']['fields'] = $this->buildOverride($fields);
    $form['override']['fields']['#prefix'] = '<div id="override-wrapper">';
    $form['override']['fields']['#suffix'] = '</div>';

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Save configuration'),
      '#button_type' => 'primary',
    ];
    $form['actions']['reset'] = [
      '#type' => 'submit',
      '#value' => $this->t('Reset'),
      '#button_type' => 'danger',
      '#submit' => ['::reset'],
    ];

    return $form;
  }

  protected function buildSelector($form, ViewEntityInterface $view) {
    $base_table = $view->get('base_table');

    $form['select_field'] = [
      '#type' => 'table',
      '#header' => [$this->t('Field'), $this->t('Group')],
    ];
  
    $fields = \Drupal::service('entity_filter.manager')->fetchFields($base_table, 'field', FALSE, 'field');

    // Add option search
    $form['controls'] = [
      '#theme_wrappers' => ['container'],
      '#attributes' => ['class' => ['views-filterable-options-controls']],
    ];
    $form['controls']['options_search'] = [
      '#type' => 'textfield',
      '#size' => 20,
      '#title' => $this->t('Search'),
    ];
    $groups = ['all' => $this->t('- All -')];
    $form['controls']['group'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#options' => [],
    ];

    foreach ($fields as $key => $field) {
      list($key_table, $key_field) = explode('.', $key);
      if (in_array($key_field, ['attachments', 'id', 'uuid', 'picture', 'langcode', 'picture_target_id', 'picture_alt',
        'picture_height', 'picture_title', 'picture_width', 'pinyin', 'attachments_display', 'attachments_description',
        'attachments_target_id', 'default_langcode', 'rendered_entity', 'operations']) || strstr($key_field, 'delete_') || strstr($key_field, 'edit_') ||
      strstr($key_field, 'view_') || strstr($key_field, 'bulk_form')) {
        continue;
      }

      $form['select_field'][$key]['#attributes']['class'][] = 'filterable-option';

      $form['select_field'][$key]['field'] = [
        '#type' => 'link',
        '#title' => $field['title'],
        '#url' => Url::fromRoute('views_template.fields_override_config', [
          'view' => $view->id(),
          'field_id' => $key,
        ]),
        '#attributes' => [
           // 'title' class 用于 AJAX 快速搜索.
           // @see views_fields_override_form.js
          'class' => ['title', 'use-ajax'],
          'data-id' => $key,
        ],
      ];
      $form['select_field'][$key]['group'] = [
        '#markup' => $field['group'],
      ];

      $group = substr(md5($field['group']), 0, 5);
      $form['select_field'][$key]['#attributes']['class'][] = $group;
      $groups[$group] = $field['group'];
    }

    $form['controls']['group']['#options'] = $groups;

    return [$form['controls'], $form['select_field']];
  }
  
  protected function buildFieldOptions() {
    $field_options = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'field_option_content',
        'class' => ['hidden'],
      ],
    ];
    $field_options['options_form'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'field_option',
      ],
    ];
    $field_options['actions']['#type'] = 'container';
    $field_options['actions']['#attributes']['class'][] = 'inline-container';
    $field_options['actions']['save'] = [
      '#type' => 'button',
      '#value' => '保存',
      '#button_type' => 'primary',
      '#ajax' => [
        'callback' => '::saveField'
      ],
    ];
    $field_options['actions']['cancel'] = [
      '#type' => 'button',
      '#value' => $this->t('Cancel'),
      '#attributes' => [
        'id' => 'field_option_cancel',
        'class' => ['btn-warning'],
      ],
    ];
    
    return $field_options;
  }
  
  /**
   * AJAX callback
   */
  public function saveField(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    
    $response->addCommand(new ReplaceCommand('#override-wrapper', $form['override']['fields']));
    
    $response->addCommand(new InvokeCommand('#field_option_content', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#override-wrapper', 'removeClass', ['hidden']));
    
    return $response;
  }

  protected function buildOverride($fields) {
    $build = [
      '#type' => 'table',
      '#header' => [
        'field' => $this->t('Field'),
        'operations' => $this->t('Operations'),
        'weight' => $this->t('Weight'),
        '',
      ],
      '#tabledrag' => [
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'weight',
        ],
      ],
      '#attributes' => [
        'id' => 'override_fields_item',
      ],
    ];

    // Combine fields filter.
    $combine_fields = [];
    if ($view_default = $this->view->getDisplay('default')) {
      if (isset($view_default['display_options']['filters']['combine'])) {
        $combine_fields = $view_default['display_options']['filters']['combine']['fields'];
      }
    }
    
    $weight = 0;
    foreach ($fields as $id => $field) {
      $build[$id]['#attributes']['class'][] = 'draggable';
      $build[$id]['#weight'] = $weight;

      if (isset($field['label'])) {
        $label = $field['label'];
      }
      else {
        $label = $field['field'];
      }
      
      if (strstr($id, 'bulk_form')) {
        $build[$id]['label'] = [
          '#type' => 'checkbox',
          '#disabled' => TRUE,
        ];
      }
      else {
        $build[$id]['label'] = [
          '#type' => 'item',
          '#markup' => $label,
          '#size' => 16,
        ];
      }

      if (in_array($id, $combine_fields) || $id == 'operations' || strstr($id, 'bulk_form')) {
        $build[$id]['operations'] = [
          '#markup' => '',
        ];
      }
      else {
        $build[$id]['operations'] = [
          '#type' => 'link',
          '#title' => $this->t('Delete'),
          '#url' => Url::fromRoute('<none>'),
          '#attributes' => ['field_item_delete' => ['delete']],
        ];
      }

      $build[$id]['weight'] = [
        '#type' => 'weight',
        '#default_value' => $weight,
        '#attributes' => ['class' => ['weight']],
      ];

      $build[$id]['options'] = [
        '#type' => 'hidden',
        '#default_value' => Json::encode($field),
      ];
      
      if (isset($field['exclude']) && $field['exclude']) {
        $build[$id]['#attributes']['class'][] = 'hidden';
      }

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

    //通过 $form_state 获取 fields 包含删除的栏目.
    $data = \Drupal::request()->get('fields');
    $fields = [];
    foreach ($data as $key => $value) {
      if ($key != 'operations' || $key == 0) {
        $fields[$key] = Json::decode($value['options']);
      }
    }
    
    if (isset($data['operations'])) {
      //Operations 固定在列表末.
      $operations = $data['operations'];
      $fields['operations'] = Json::decode($operations['options']);
    }
    $view_override['fields'] = $fields;

    \Drupal::service('views_template.manager')->setViewOverride($this->view, $view_override);
  }

  /**
   * Submit callback.
   */
  public function reset(array &$form, FormStateInterface $form_state) {
    \Drupal::service('views_template.manager')->deleteFieldsOverride($this->view);
  }

}
