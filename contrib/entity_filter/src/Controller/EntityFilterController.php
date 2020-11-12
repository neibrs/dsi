<?php

namespace Drupal\entity_filter\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Url;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;

class EntityFilterController extends ControllerBase {

  /**
   * 条件设置添加自定义字段设置AJAX处理： /entity_filter/add_handler_config/{base_table}/{handler_type}/{key}
   */
  public function viewsAddHandlerConfig($base_table, $handler_type, $key) {
    $response = new AjaxResponse();
  
    list($key_table, $key_field) = explode('.', $key);
    $handler_config = [
      'table' => $key_table,
      'field' => $key_field,
    ];
    $options = $this->configHandlerBuildForm($base_table, $handler_type, $handler_config);
  
    $handler = $options['#handler'];
    
    $step3 = [
      '#type' => 'container',
      '#attributes' => ['id' => 'options_form'],
    ];
  
    $types = ViewExecutable::getHandlerTypes();
    $step3['title'] = [
      '#markup' => $this->t('Configure @type: @item', ['@type' => $types[$handler_type]['lstitle'], '@item' => $handler->adminLabel()]),
      '#theme_wrappers' => ['container'],
      '#attributes' => ['id' => 'custom_condition_step3_title'],
    ];

    // Add #name property for $form elements
    $step3['options'] = $options;
    $step3['options']['options']['#attributes']['data-plugin-id'] = $handler->getPluginId();
  
    $response->addCommand(new InvokeCommand('#custom_condition_step1', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step2', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step4', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step3', 'removeClass', ['hidden']));
    $response->addCommand(new ReplaceCommand('#options_form', $step3));

    return $response;
  }

  /**
   * 条件设置更新自定义字段设置AJAX处理： /entity_filter/handler_config/{base_table}/{handler_type}/{handler_config}
   */
  public function updateHandlerConfig($base_table, $handler_type, $handler_config) {
    $response = new AjaxResponse();
  
    // 预置条件通过 Json 字符串传输配置信息.
    if (unserialize($handler_config)) {
      $handler_config = unserialize($handler_config);
    }
    else {
      $handler_config = Json::decode($handler_config);
    }
    $options = $this->configHandlerBuildForm($base_table, $handler_type, $handler_config);
    
    $handler = $options['#handler'];
  
    $step3 = [
      '#type' => 'container',
      '#attributes' => ['id' => 'options_form'],
    ];
  
    $types = ViewExecutable::getHandlerTypes();
    $step3['title'] = [
      '#markup' => $this->t('Configure @type: @item', ['@type' => $types[$handler_type]['lstitle'], '@item' => $handler->adminLabel()]),
      '#theme_wrappers' => ['container'],
      '#attributes' => ['id' => 'custom_condition_step3_title'],
    ];
  
    // Add #name property for $form elements
    $step3['options'] = $options;
    $step3['options']['options']['#attributes']['data-plugin-id'] = $handler->getPluginId();
  
    $response->addCommand(new InvokeCommand('#custom_condition_step1', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step2', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step4', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step3', 'removeClass', ['hidden']));
    $response->addCommand(new ReplaceCommand('#options_form', $step3));
  
    return $response;
  
  }
  
  /**
   * 通用的生成 options 表单.
   *
   * @see \Drupal\views_ui\Form\Ajax\ConfigHandler::buildForm()
   */
  protected function configHandlerBuildForm($base_table, $handler_type, $handler_config) {
    // Build form from plugin's buildOptionsForm.
    $handler = \Drupal::service('plugin.manager.views.' . $handler_type)->getHandler($handler_config);
    
    // init的作用：设置Drupal\views\Plugin\views\filter\Bundle对象的entityType属性。
    $view = \Drupal::entityTypeManager()->getStorage('view')->create([
      'base_table' => $base_table,
    ]);
    $view_executable = Views::executableFactory()->get($view);
    $display = $view_executable->getDisplay();
    
    $form['options'] = [
      '#tree' => TRUE,
      '#theme_wrappers' => ['container'],
      '#attributes' => ['class' => ['scroll'], 'data-drupal-views-scroll' => TRUE],
    ];
  
    // 从 _POST 获取用户输入数据，供 $handler->buildOptionsForm 初始化表单.
    $post = \Drupal::request()->request;
    if ($post->has('options')) {
      $options = $post->get('options');
    }

    // 获取可选择的关系
    $relationships = \Drupal::service('entity_filter.manager')->fetchRelationships($base_table);

    foreach ($relationships as $key => $relationship) {
      list($relationship_table, $relationship_field) = explode('.', $key);
      // If this relationship is valid for this type, add it to the list.
      $data = Views::viewsData()->get($relationship_table);
      if (isset($data[$relationship_field]['relationship']['base']) && $base = $data[$relationship_field]['relationship']['base']) {
        $base_fields = Views::viewsDataHelper()->fetchFields($base, $handler_type);
        if (isset($base_fields[$handler_config['table']. '.' . $handler_config['field']])) {
          $relationship_options[$relationship_field] = $relationship['title'];
        }
      }
    }

    if (!empty($relationship_options)) {
      $base_fields = Views::viewsDataHelper()->fetchFields($base_table, $handler_type);
      if ($base_table == $handler_config['table'] && isset($base_fields[$base_table . '.' . $handler_config['field']])) {
        $relationship_options = array_merge(['none' => $this->t('Do not use a relationship')], $relationship_options);
      }
      $rel = empty($handler_config['relationship']) ? 'none' : $handler_config['relationship'];
      if (empty($relationship_options[$rel])) {
        // Pick the first relationship.
        $rel = key($relationship_options);
        // We want this relationship option to get saved even if the user
        // skips submitting the form.
        $handler_config['relationship'] = $rel;
      }
      $form['options']['relationship'] = [
        '#type' => 'select',
        '#title' => $this->t('Relationship'),
        '#options' => $relationship_options,
        '#weight' => -500,
      ];
      // 选择最终学历/学位/职称等后，需要过滤可选的资质类型。
      if (isset($options) && !empty($options['relationship'])) {
        $form['options']['relationship']['#default_value'] = $options['relationship'];
      }
      $route_match = \Drupal::routeMatch();
      $form['options']['relationship']['#ajax'] = [
        'url' => Url::fromRoute($route_match->getRouteName(), $route_match->getRawParameters()->all()),
      ];
    }
    else {
      $form['options']['relationship'] = [
        '#type' => 'value',
        '#value' => 'none',
      ];
    }
  
    // Build the form.
    $form_state = new FormState();
    if (isset($options)) {
      $form_state->setUserInput(['options' => $options]);
    }

    // Fix Argument 3 passed to Drupal\Core\Entity\Element\EntityAutocomplete::processEntityAutocomplete() must be of the type array.
    $complete_form = [];
    $form_state->setCompleteForm($complete_form);

    // 需要先设置好 display options 的 relationship，避免 $handler->init() 报错.
    \Drupal::service('entity_filter.manager')->addHandlersRelationshipToDisplay($display, [$handler_config]);
    $handler->init($view_executable, $display, $handler_config);
  
    // 将插件选项构造到表单的 $form['options'] 里
    $handler->buildOptionsForm($form['options'], $form_state);

    unset($form['options']['expose_button']);
    unset($form['options']['more']);

    // 设置默认的 admin_label
    $title = isset($this->definition['title short']) ? $handler->definition['title short'] : $handler->definition['title'];
    $admin_label = $this->t('@group: @title', ['@group' => $handler->definition['group'], '@title' => $title]);
    $form['options']['admin_label']['admin_label']['#default_value'] = $admin_label;

    // 添加 filter 配置需要的数据.
    $form['options']['id'] = ['#type' => 'hidden', '#value' => $handler->options['id']];
    $form['options']['table'] = ['#type' => 'hidden', '#value' => $handler_config['table']];
    $form['options']['field'] = ['#type' => 'hidden', '#value' => $handler_config['field']];
    $form['options']['plugin_id'] = ['#type' => 'hidden', '#value' => $handler->getPluginId()];
    $form['options']['entity_type'] = ['#type' => 'hidden', '#value' => $handler->definition['entity_type']];

    $options = \Drupal::formBuilder()->doBuildForm(NULL, $form, $form_state);
  
    // 外面的程序要用到 handler
    $options['#handler'] = $handler;
  
    return $options;
  }

}