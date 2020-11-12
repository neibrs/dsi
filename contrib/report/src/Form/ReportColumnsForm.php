<?php

namespace Drupal\report\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Url;
use Drupal\views\Views;

class ReportColumnsForm extends ReportFiltersForm {
  
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $report = NULL, $setting_name = NULL) {
    $form = parent::buildForm($form, $form_state, $report, $setting_name);
  
    // Make columns and rows label editable.
    $form['#attached']['library'][] = 'report/report_filters_form';
  
    return $form;
  }
  
  /**
   * {@inheritdoc}
   */
  protected function processAjax(FormStateInterface $form_state, $filters = NULL) {
    $filters = parent::processAjax($form_state, $filters);
    
    // 列设置 field.
    if ($form_state->getValue('op') == '保存字段设置') {
      $field_setting = $form_state->getUserInput()['field_setting'];
      if ($filters = \Drupal::request()->get('filters')) {
        if (isset($filters[$field_setting['id']])) {
          list($statistics_table, $statistics_field) = explode('.', $field_setting['statistics_field']);
          $field = [
            'id' => $statistics_field,
            'table' => $statistics_table,
            'field' => $statistics_field,
            'relationship' => $field_setting['statistics_relationship'],
            'group_type' => $field_setting['statistics_method'],
            'plugin_id' => 'standard',
          ];
          
          $filters[$field_setting['id']]['field'] = $field;
        }
      }
    }
    
    return $filters;
  }
  
  /**
   * {@inheritdoc}
   */
  protected function adminLabel($filter, $base_table) {
    if (!($filter && $base_table)) {
      return;
    }
    
    $handler = \Drupal::service('plugin.manager.views.filter')->getHandler($filter);
    
    // init的作用：设置Drupal\views\Plugin\views\filter\Bundle对象的entityType属性。
    $view = \Drupal::entityTypeManager()->getStorage('view')->create([
      'base_table' => $base_table,
    ]);
    $view_executable = Views::executableFactory()->get($view);
    $display = $view_executable->getDisplay();
    
    // 需要先设置好 display options 的 relationship，避免 $handler->init() 报错.
    \Drupal::service('entity_filter.manager')->addHandlersRelationshipToDisplay($display, [$filter]);
    $handler->init($view_executable, $display, $filter);
    
    $admin_label = $handler->adminSummary();
    $admin_label = str_replace('(', '',$admin_label);
    $admin_label = str_replace(')', '',$admin_label);
    
    // 删除 admin_label 包含的 operator.
    $operator = $handler->operatorOptions('short')[$handler->operator];
    if ($operator instanceof TranslatableMarkup) {
      $operator = \Drupal::translation()->translateString($operator);
    }
    if (!strstr($admin_label, $operator)) {
      $operator = $handler->operatorOptions('short_single')[$handler->operator];
    }
    $admin_label = str_replace($operator, '', $admin_label);
  
    return trim($admin_label);
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildCustomConditionBuilder() {
    $form = parent::buildCustomConditionBuilder();
    
    // 列设置 field.
    $step4 = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'custom_condition_step4',
        'class' => ['hidden'],
      ],
    ];
    $step4['field_setting'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'field_setting_form'],
    ];
  
    $step4['actions']['#type'] = 'container';
    $step4['actions']['#attributes']['class'][] = 'inline-container';
    $step4['actions']['save'] = [
      '#type' => 'button',
      '#button_type' => 'primary',
      '#value' => '保存字段设置',
      '#ajax' => [
        'callback' => '::saveField'
      ],
    ];
    $step4['actions']['cancel'] = [
      '#type' => 'button',
      '#value' => $this->t('Cancel'),
      '#attributes' => [
        'id' => 'custom_condition_step4_cancel',
        'class' => ['btn-warning'],
      ],
    ];
  
    $form['custom_condition']['step4'] = $step4;
    
    return $form;
  }
  
  /**
   * AJAX callback
   */
  public function saveField(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    
    $response->addCommand(new ReplaceCommand('#filter-items-content', $form['filters_content']));
  
    $response->addCommand(new InvokeCommand('#custom_condition_step4', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step1', 'removeClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step2', 'removeClass', ['hidden']));
    
    return $response;
  }
  
  /**
   * {@inheritdoc}
   */
  public function buildFilters($filters) {
    $build = [
      '#type' => 'table',
      '#header' => [
        $this->t('Item'),
        '',
        '',
        $this->t('Condition'),
        $this->t('Weight'),
        $this->t('Operations'),
        '',
        '',
      ],
      '#attributes' => [
        'data-theme' => 'report_filters_form',
        'id' => 'filter-items',
      ],
      '#tabledrag' => [
        [
          'action' => 'match',
          'relationship' => 'parent',
          'group' => 'filter-parent',
          'subgroup' => 'filter-parent',
          'source' => 'filter-id',
          'hidden' => TRUE,
        ],
        [
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'filter-weight',
        ],
      ],
    ];
    
    foreach ($filters as $id => $filter) {
      if (!isset($filter['parent']) && !isset($filter['depth'])) {
        $filters[$id] = $filter;
    
        $filters[$id]['parent'] = '';
        $filters[$id]['depth'] = 1;
        $filters[$id]['weight'] = 0;
      }
      
      if (!isset($filter['depth'])) {
        $filters[$id]['depth'] = $this->findDepth($filters, $filter);
      }
      
      if (isset($filter['admin_label'])) {
        $filters[$id]['filter']['admin_label'] = $filter['admin_label'];
      }
      
      if (!is_array($filter['filter'])) {
        $filters[$id]['filter'] = Json::decode($filter['filter']);
      }
  
      if (!is_array($filter['field'])) {
        $filters[$id]['field'] = Json::decode($filter['field']);
      }
    }

    $this->buildTreeFilters($filters, $build);
  
    return $build;
  }
  
  public function buildTreeFilters($filters, &$build) {
    foreach ($filters as $id => $filter) {
      $build[$id]['admin_label'] = [
        [
          '#theme' => 'indentation',
          '#size' => $filter['depth'] - 1,
        ],
        [
          '#type' => 'textfield',
          '#value' => $filter['filter']['admin_label'],
          '#size' => '',
        ],
      ];
  
      $build[$id]['id'] = [
        '#type' => 'hidden',
        '#value' => $filter['filter']['id'],
        '#attributes' => ['class' => ['filter-id']],
      ];
    
      $build[$id]['parent'] = [
        '#type' => 'hidden',
        '#default_value' => $filter['parent'],
        '#attributes' => ['class' => ['filter-parent'],]
      ];
      
      // 生成条件描述字符串.
      $condition = $filter['filter'];
      unset($condition['admin_label']);
      $readable = \Drupal::service('entity_filter.manager')->getReadableFiltersString($this->base_table, [$condition]);
      $build[$id]['condition_label'] = [
        '#markup' => $readable,
      ];
  
      $build[$id]['weight'] = [
        '#type' => 'weight',
        '#default_value' => $filter['weight'],
        '#attributes' => ['class' => ['filter-weight']],
      ];
  
      $build[$id]['operations'] = [
        '#type' => 'container',
        '#attributes' => [
          'class' => ['links-dropbutton-operations'],
        ],
      ];
      $build[$id]['operations']['update'] = [
        '#type' => 'link',
        '#title' => $this->t('Edit'),
        '#url' => URL::fromRoute('entity_filter.handler_config', [
          'base_table' => $this->base_table,
          'handler_type' => 'filter',
          'handler_config' => serialize($filter['filter']),
        ]),
        '#attributes' => ['class' => ['text-primary', 'use-ajax']],
      ];
      
      // 列设置 field.
      if ($this->setting_name == 'columns') {
        $report_settings = $this->entity->get('settings');
        $build[$id]['operations']['field_setting'] = [
          '#type' => 'link',
          '#title' => $this->t('Field setting'),
          '#url' => URL::fromRoute('report.column.field_setting', [
            'base_table' => $this->base_table,
            'column_filter' => serialize($filter['filter']),
            'column_field' => serialize(empty($filter['field']) ? $report_settings['field'] : $filter['field']),
          ]),
          '#attributes' => ['class' => ['text-aqua', 'use-ajax']],
        ];
      }
      
      $build[$id]['operations']['delete'] = [
        '#markup' => '<a class="filter-delete text-red">' . $this->t('Delete') . '</a>',
      ];
    
      $build[$id]['filter'] = [
        '#type' => 'hidden',
        '#value' => Json::encode($filter['filter']),
      ];
      
      $build[$id]['field'] = [
        '#type' => 'hidden',
        '#value' => isset($filter['field']) && !empty($filter['field']) ? Json::encode($filter['field']) : '',
      ];
    
      if (isset($filter['subtree']) && $subtree = $filter['subtree']) {
        $this->buildTreeFilters($subtree, $build);
      }
    
      $build[$id]['#attributes']['class'][] = 'draggable';
    }
  
  }
  
  public function findDepth($filters, $filter, $depth = 1) {
    if (!empty($filter['parent'])) {
      $depth = $depth + 1;
      $depth = $this->findDepth($filters, $filters[$filter['parent']], $depth);
    }
    
    return $depth;
  }
  
  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $filters = $form_state->getValue('filters');
  
    if (!$filters) {
      $filters = [];
    }
  
    // Delete removed item.
    $input = $form_state->getUserInput();
    $inputs = [];
    if ($input['filters']) {
      $inputs = array_filter($input['filters'], function ($c) {
        // If the config rows was removed by user, the filter hidden input will not exists.
        return isset($c['filter']);
      });
    }
    else {
      $inputs = [];
    }
  
    $filters = array_intersect_key($filters, $inputs);
    foreach ($filters as $id => $filter) {
      // Decode the filter.
      if (!is_array($filter['filter'])) {
        $filter['filter'] = Json::decode($filter['filter']);
      }
      
      // Decode the filter.
      if (isset($filter['field']) && !is_array($filter['field'])) {
        $filter['field'] = Json::decode($filter['field']);
      }
      
      // If admin_label is editable.
      if (isset($filter['admin_label'])) {
        $filter['filter']['admin_label'] = $filter['admin_label'][1];
        unset($filter['admin_label']);
      }
    
      if (isset($filter['id'])) {
        $filter['filter']['id'] = $filter['id'];
        unset($filter['id']);
      }
      else {
        $filter['filter']['id'] = $id;
      }
    
      $parent = $filter['parent'];
      if (empty($parent)) {
        $filter['depth'] = 1;
      }
      else {
        $this->findParent($filters, $filter);
        
        unset($filters[$id]);
        continue;
      }
    
      $filters[$id] = $filter;
    }
  
    $form_state->setValue('filters', $filters);
  
  }
  
  public function findParent(&$filters, $item) {
    if (array_key_exists($item['parent'], $filters)) {
      $item['depth'] =  $filters[$item['parent']]['depth'] + 1;
      $filters[$item['parent']]['subtree'][$item['filter']['id']] = $item;
      return;
    }
    
    foreach ($filters as &$filter) {
      if (isset($filter['subtree'])) {
        $this->findParent($filter['subtree'], $item);
      }
    }
  }

  
}
