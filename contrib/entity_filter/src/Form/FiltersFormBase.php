<?php

namespace Drupal\entity_filter\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\views\Views;
use Drupal\views\ViewsData;
use Symfony\Component\DependencyInjection\ContainerInterface;


abstract class FiltersFormBase extends FormBase {

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * @var \Drupal\views\ViewsData
   */
  protected $viewsData;

  public function __construct(EntityTypeManagerInterface $entity_type_manager, ViewsData $views_data) {
    $this->entityTypeManager = $entity_type_manager;
    $this->viewsData = $views_data;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('views.views_data')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'filters_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $base_table = NULL, $filters = NULL) {
    if (!$filters) {
      $filters = [];
    }
    
    $this->base_table = $base_table;
    
    $form['#theme'] = 'entity_filter_form';
    $form['#attached']['library'][] = 'entity_filter/filters_form';

    $form['predefined_filters'] = $this->buildPredefinedFilters();

    $form['custom_condition_builder'] = $this->buildCustomConditionBuilder();

    // 处理 AJAX 提交.
    $filters = $this->processAjax($form_state, $filters);
  
    $form['filters_content'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'filter-items-content',
      ],
    ];
    $form['filters_content']['filters'] = $this->buildFilters($filters);

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }
  
  /**
   * 处理 Ajax.
   */
  protected function processAjax(FormStateInterface $form_state, $filters = NULL) {
    // 保存条件项.
    if ($form_state->getValue('op') == '保存条件项') {
      $filters = [];
      $filters_ids = [];
      if ($item_filters = \Drupal::request()->get('filters')) {
        foreach ($item_filters as $id => $filter) {
          $filter['filter'] = Json::decode($filter['filter']);
          $filters[$filter['filter']['id']] = $filter;
        
          // 合同到期提醒、试用期满链接至列表时，传输的过滤条件 id 为数字，需要转化成字符串.
          // @see views_template_views_pre_view().
          $filters_ids[] = (string)$filter['filter']['id'];
        }
      }
    
      $filter = $form_state->getUserInput()['options'];
      // 生成条件描述串
      $filter['admin_label'] = $this->adminLabel($filter, $this->base_table);
    
      // 如果修改 options，则替换原来的 filter.
      if (isset($filter['id']) && !empty($filter['id']) && in_array($filter['id'], $filters_ids)) {
        $filters[$filter['id']]['filter'] = $filter;
      }
      else {
        $filter['id'] = $this->getFilterId($filter, $filters_ids);
      
        $filters[$filter['id']]['filter'] = $filter;
      }
    }
    
    return $filters;
  }
  
  /**
   * 生成 filter 唯一 ID.
   */
  protected function getFilterId($filter, $filters_ids) {
    // 生成 filter_id.
    $filter_id = NULL;
    if (isset($filter['relationship'])) {
      $filter_id = $filter['relationship'] . '_';
    }
    $filter_id = $filter_id . $filter['field'];
    $id = $filter_id;
    while (in_array($id, $filters_ids)) {
      $id = $filter_id . '_' . mt_rand();
    }
    
    return $id;
  }
  
  /**
   * 获得条件项描述串
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
    
    // 获取 filter 关联的关系和字段.
    $relationships = [];
    foreach ($view_executable->display_handler->getHandlers('relationship') as $id => $relationship_handler) {
      $relationships[$id] = $relationship_handler->adminLabel();
    }
    $field_name = $handler->adminLabel(TRUE);
    if (!empty($filter['relationship']) && !empty($relationships[$filter['relationship']])) {
      $field_name = '(' . $relationships[$filter['relationship']] . ') ' . $field_name;
    }
  
    $description = $handler->adminSummary();
    $admin_label = $field_name . (empty($description) ? '' : " ($description)");
  
    return $admin_label;
  }

  /**
   * Build the predefined filters selection tree.
   *
   * @return array
   *   The render array.
   */
  protected function buildPredefinedFilters() {
    $build = [
      '#theme' => 'tree',
      '#items' => [],
    ];

    /** @var \Drupal\entity_filter\Entity\PredefinedFilterInterface $predefined_filter */
    $predefined_filter = $this->entityTypeManager->getStorage('predefined_filter')->load($this->base_table);
    // 如果该表无 predefined_filter, 返回空内容.
    if (!$predefined_filter) {
      return [];
    }

    $filters = $predefined_filter->getFilters();

    $this->buildFiltersSelector($build, $filters);

    // Add relationships predefined filter
    $relationships = $predefined_filter->getRelationships();
    foreach ($relationships as $id => $relationship) {
      if (isset($relationship['entity_type'])) {
        $entity_type_id = $relationship['entity_type'];
        $entity_type = $this->entityTypeManager->getDefinition($entity_type_id);
        $data = $this->viewsData->get($entity_type->getDataTable() ?: $entity_type->getBaseTable());
      }
      else {
        $data = $this->viewsData->get($relationship['table']);
      }

      $views_relationship = $data[$relationship['field']]['relationship'];
      /** @var \Drupal\entity_filter\Entity\PredefinedFilterInterface $predefined_filter */
      $predefined_filter = $this->entityTypeManager->getStorage('predefined_filter')->load($views_relationship['base']);
      if (!$predefined_filter) {
        continue;
      }
      \Drupal::moduleHandler()->alter('predefined_filter', $predefined_filter, $relationship);
      
      $filters = $predefined_filter->getFilters();
      if (empty($filters)) {
        continue;
      }

      $build['#items'][$id] = [
        'title' => $views_relationship['label'],
        'below' => [],
        'is_expanded' => TRUE,
        'attributes' => new Attribute(),
      ];

      $this->buildFiltersSelector($build, $filters, $id);
    }

    $build['#attached']['library'][] = 'eabax_core/tree';

    return $build;
  }

  protected function buildFiltersSelector(&$build, $filters, $relationship = NULL) {
    foreach ($filters as $id => $filter) {
      $filter['id'] = $filter['field'] . '_' . $id;
      
      if ($relationship) {
        $filter_id = $relationship . '-' . $id;
        $parent = &$build['#items'][$relationship]['below'];

        $filter['relationship'] = $relationship;
      }
      else {
        $filter_id = $id;
        $parent = &$build['#items'];
      }

      $labels = explode(':', $filter['admin_label']);
      $size = sizeof($labels);
      for($i = 0; $i < $size; $i++) {
        if ($i < $size - 1) {
          if (!isset($parent[$labels[$i]])) {
            $parent[$labels[$i]] = [
              'title' => $labels[$i],
              'below' => [],
              'is_expanded' => TRUE,
              'attributes' => new Attribute(),
            ];
          }
          $parent = &$parent[$labels[$i]]['below'];
        }
        else {
          $parent[$labels[$i]] = [
            'title' => $labels[$i],
            'attributes' => new Attribute(['data-id' => $filter_id]),
          ];
        }
      }

      $build['#attached']['drupalSettings']['predefinedFilters'][$filter_id] = $filter;
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function buildCustomConditionBuilder() {
    $form['custom_condition']['step1'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => ['custom_condition_step1'],
      ],
    ];
    $form['custom_condition']['step1']['add_custom_condition'] = [
      '#type' => 'link',
      '#title' => $this->t('Add custom condition'),
      '#url' => Url::fromRoute('<none>'),
      '#attributes' => [
        'id' => 'add_custom_condition',
        'class' => ['button', 'button button--primary btn btn-primary btn-sm'],
      ],
    ];

    $form['custom_condition']['step2'] = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'custom_condition_step2',
        'class' => ['hidden'],
      ],
    ];

    $form['custom_condition']['step2']['controls'] = [
      '#theme_wrappers' => ['container'],
      '#attributes' => ['class' => ['container-inline', 'views-filterable-options-controls']],
    ];
    $form['custom_condition']['step2']['controls']['options_search'] = [
      '#type' => 'textfield',
      '#size' => 30,
      '#title' => $this->t('Search'),
    ];

    $groups = ['all' => $this->t('- All -')];
    $form['custom_condition']['step2']['controls']['group'] = [
      '#type' => 'select',
      '#title' => $this->t('Category'),
      '#options' => [],
    ];

    $filters = \Drupal::service('entity_filter.manager')->fetchFields($this->base_table, 'filter', FALSE, 'filter');

    if (isset($this->view)) {
      $default_filter = $this->view->get('display')['default']['display_options']['filters'];
      if (in_array('type', $default_filter)) {
        $types = $default_filter['type']['value'];
      }
    }

    $form['custom_condition']['step2']['item'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Title'),
        $this->t('Category'),
      ],
    ];
    foreach ($filters as $key => $filter) {

      if ($filter['help'] instanceof TranslatableMarkup) {
        $arguments = $filter['help']->getArguments();
        if (!empty($types) && !empty($arguments)) {
            if (!in_array($arguments['@bundles'], $types)) {
              continue;
          }
        }
      }

      list($key_table, $key_field) = explode('.', $key);
      if (in_array($key_field, ['attachments_target_id', 'attachments_description', 'attachments_display', 'id', 'langcode', 'picture_target_id', 'picture_alt', 'picture_height', 'picture_title', 'picture_width', 'uuid', 'default_langcode', 'pinyin', 'changed', 'created'])) {
        continue;
      }

      $form['custom_condition']['step2']['item'][$key]['#attributes']['class'][] = 'filterable-option';

      $form['custom_condition']['step2']['item'][$key]['title'] = [
        '#type' => 'link',
        '#title' => $filter['title'],
        '#url' => Url::fromRoute('entity_filter.add_handler_config', [
          'base_table' => $this->base_table,
          'handler_type' => 'filter',
          'key' => $key,
        ]),
        '#attributes' => ['class' => ['title', 'use-ajax']],
      ];
      $form['custom_condition']['step2']['item'][$key]['category'] = [
        '#markup' => $filter['group'],
      ];

      $group = substr(md5($filter['group']), 0, 5);
      $form['custom_condition']['step2']['item'][$key]['#attributes']['class'][] = $group;
      $groups[$group] = $filter['group'];
    }

    $form['custom_condition']['step2']['controls']['group']['#options'] = $groups;

    $step3 = [
      '#type' => 'container',
      '#attributes' => [
        'id' => 'custom_condition_step3',
        'class' => ['hidden'],
      ],
    ];
    $step3['options_form'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'options_form'],
    ];
    
    $step3['actions']['#type'] = 'container';
    $step3['actions']['#attributes']['class'][] = 'inline-container';
    $step3['actions']['save'] = [
      '#type' => 'button',
      '#button_type' => 'primary',
      '#value' => '保存条件项',
      '#ajax' => [
        'callback' => '::saveFilter'
      ],
    ];
    $step3['actions']['return'] = [
      '#type' => 'button',
      '#button_type' => 'danger',
      '#value' => $this->t('Return'),
      '#attributes' => [
        'id' => 'custom_condition_step3_return',
      ],
    ];
  
    $step3['actions']['cancel'] = [
      '#type' => 'button',
      '#value' => $this->t('Cancel'),
      '#attributes' => [
        'id' => 'custom_condition_step3_cancel',
        'class' => ['btn-warning'],
      ],
    ];
  
    $form['custom_condition']['step3'] = $step3;

    return $form;
  }
  
  /**
   * AJAX callback
   */
  public function saveFilter(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    
    $response->addCommand(new ReplaceCommand('#filter-items-content', $form['filters_content']));
    
    $response->addCommand(new InvokeCommand('#custom_condition_step3', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step1', 'removeClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step2', 'removeClass', ['hidden']));
    
    return $response;
  }
  
  /**
   * {@inheritdoc}
   */
  protected function buildFilters($filters) {
    $build= [
      '#type' => 'table',
      '#header' => [$this->t('Item'), $this->t('Operations'), ''],
      '#attributes' => [
        'id' => 'filter-items',
        'data-theme' => 'entity_filter',
      ],
    ];
    foreach ($filters as $id => $filter) {
      if (isset($filter['filter'])) {
        $filter = $filter['filter'];
      }
      
      $build[$id]['#attributes']['data-id'] = $id;

      $build[$id]['admin_label'] = [
        '#markup' => $filter['admin_label'],
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
          'handler_config' => serialize($filter),
        ]),
        '#attributes' => ['class' => ['title', 'use-ajax']],
      ];
      $build[$id]['operations']['delete'] = [
        '#markup' => '<a class="filter-delete text-red">' . $this->t('Delete') . '</a>',
      ];
      
      $build[$id]['filter'] = [
        '#type' => 'hidden',
        '#value' => Json::encode($filter),
      ];
    }

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);

    $filters = $form_state->getValue('filters');

    if (!$filters) {
      $filters = [];
    }

    // Delete removed item.
    $input = $form_state->getUserInput();
    $inputs = [];
    if ($input['filters']) {
      $inputs = array_filter($input['filters'], function($c) {
        // If the config rows was removed by user, the filter hidden input will not exists.
        return isset($c['filter']);
      });
    }
    else {
      $inputs = [];
    }

    $filters = array_intersect_key($filters, $inputs);

    $filters = array_map(function ($item) {
      // Decode the filter.
      if (!is_array($item['filter'])) {
        $item['filter'] = Json::decode($item['filter']);
      }
      // If admin_label is editable.
      if (isset($item['admin_label'])) {
        $item['filter']['admin_label'] = $item['admin_label'];
      }
      return $item['filter'];
    }, $filters);

    $form_state->setValue('filters', $filters);
  }

}
