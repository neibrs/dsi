<?php

namespace Drupal\report\Controller;

use Collator;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InvokeCommand;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Form\FormState;
use Drupal\Core\Link;
use Drupal\Core\Url;
use Drupal\report\Entity\ReportInterface;
use Drupal\views\Views;

class ReportController extends ControllerBase {

  public function addPage() {
    $build = [
      '#theme' => 'entity_add_list',
    ];

    /** @var \Drupal\Component\Plugin\PluginManagerInterface $report_manager */
    $report_manager = \Drupal::service('plugin.manager.report');
    $plugins = $report_manager->getDefinitions();
    foreach ($plugins as $plugin) {
      $build['#bundles'][$plugin['id']] = [
        'label' => $plugin['label'],
        'description' => '',
        'add_link' => Link::createFromRoute($plugin['label'], 'entity.report.add_form', [
          'plugin' => $plugin['id'],
        ]),
      ];
    }

    return $build;
  }

  /**
   * @see \Drupal\report\Plugin\ReportPluginBase::build()
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function ajaxStyle(ReportInterface $report, $style) {
    $response = new AjaxResponse();

    $build = $report->getPlugin()->$style($report);
    $response->addCommand(new ReplaceCommand('#report-' . $report->id(), $build));

    return $response;
  }
  
  /**
   * 报表列设置 field.
   */
  public function fieldSetting($base_table, $column_filter, $column_field) {
    $response = new AjaxResponse();
    
    $column_filter = unserialize($column_filter);
    $column_field = unserialize($column_field);
  
    $step4 = [
      '#type' => 'container',
      '#attributes' => ['id' => 'field_setting_form'],
    ];
    $step4['title'] = [
      '#markup' => $this->t('Field setting: @column',['@column' => $column_filter['admin_label']]),
      '#theme_wrappers' => ['container'],
      '#attributes' => ['id' => 'custom_condition_step4_title'],
    ];
  
    // 构造字段设置表单.
    $form['field_setting'] = [
      '#tree' => TRUE,
      '#theme_wrappers' => ['container'],
      '#attributes' => ['class' => ['scroll'], 'data-drupal-views-scroll' => TRUE],
    ];
    $form['field_setting']['id'] = ['#type' => 'hidden', '#value' => $column_filter['id']];
  
    // 从 _POST 获取用户输入数据，初始化表单.
    $post = \Drupal::request()->request;
    if ($post->has('field_setting')) {
      $field_settins = $post->get('field_setting');
    }
  
    // Statistics field.
    $statistics_field = $column_field['table'] . '.' . $column_field['field'];
    if (isset($field_settins) && !empty($field_settins['statistics_field'])) {
      $statistics_field = $field_settins['statistics_field'];
    }
    $fields = \Drupal::service('entity_filter.manager')->fetchFields($base_table, 'field', FALSE, 'field');
    // 删除不需要的字段.
    $options = ['none' => $this->t('-Select-')];
    foreach ($fields as $key => $field) {
      list(, $key_field) = explode('.', $key);
      if (in_array($key_field, ['attachments', 'id', 'uuid', 'created', 'changed', 'picture', 'langcode', 'picture_target_id', 'picture_alt',
          'picture_height', 'picture_title', 'picture_width', 'pinyin', 'attachments_display', 'attachments_description',
          'attachments_target_id', 'default_langcode', 'rendered_entity', 'operations']) || strstr($key_field, 'delete_') || strstr($key_field, 'edit_') ||
        strstr($key_field, 'view_') || strstr($key_field, 'bulk_form')) {
        continue;
      }
    
      $options[$key] = '(' . $field['group'] . ') ' . $field['title'];
    }
    // 拼音排序
    (new Collator('zh-CN'))->asort($options);
    
    $route_match = \Drupal::routeMatch();
    $form['field_setting']['statistics_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Statistics field'),
      '#options' => $options,
      '#value' => $statistics_field,
      '#ajax' => [
        'url' => Url::fromRoute($route_match->getRouteName(), $route_match->getRawParameters()->all()),
        'event' => 'change',
      ],
    ];
  
    //statistics_relationship
    $relationship_options = ['none' => $this->t('-Select-')];
    $predefined_filter = \Drupal::entityTypeManager()->getStorage('predefined_filter')->load($base_table);
    if (!empty($predefined_filter)) {
      $relationships = $predefined_filter->getRelationships();
    
      foreach ($relationships as $relationship) {
        $relationship_handler = Views::handlerManager('relationship')->getHandler($relationship);
        // ignore invalid/broken relationships.
        if (empty($relationship_handler)) {
          continue;
        }
      
        // If this relationship is valid for this type, add it to the list.
        $data = Views::viewsData()->get($relationship['table']);
        if (isset($data[$relationship['field']]['relationship']['base']) && $base = $data[$relationship['field']]['relationship']['base']) {
          $base_fields = Views::viewsDataHelper()->fetchFields($base, 'filter');
          if (isset($base_fields[$statistics_field])) {
            $id = isset($relationship['id']) ? $relationship['id'] : $relationship['field'];
            $relationship_options[$id] = $relationship_handler->adminLabel();
          }
        }
      }
    }
    $form['field_setting']['statistics_relationship'] = [
      '#type' => 'select',
      '#title' => $this->t('Statistics relationship'),
      '#options' => $relationship_options,
      '#value' => !empty($column_field['relationship']) ? $column_field['relationship'] : 'none',
    ];
  
    $form['field_setting']['statistics_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Statistics method'),
      '#options' => [
        'count' => $this->t('Count'),
        'count_distinct' => $this->t('Count(distinct)'),
        'sum' => $this->t('Sum'),
        'avg' => $this->t('Average'),
        'min' => $this->t('Minimum'),
        'max' => $this->t('Maximum'),
        'stddev_pop' => $this->t('Standard deviation'),
      ],
      '#value' => !empty($column_field['group_type']) ? $column_field['group_type'] : 'count',
    ];
  
    // Build the form.
    $form_state = new FormState();
    if (isset($field_settins)) {
      $form_state->setUserInput(['field_setting' => $field_settins]);
    }
  
    // Fix Argument 3 passed to Drupal\Core\Entity\Element\EntityAutocomplete::processEntityAutocomplete() must be of the type array.
    $complete_form = [];
    $form_state->setCompleteForm($complete_form);
    
    $step4['field_setting'] = \Drupal::formBuilder()->doBuildForm(NULL, $form, $form_state);
  
    $response->addCommand(new InvokeCommand('#custom_condition_step1', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step2', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step3', 'addClass', ['hidden']));
    $response->addCommand(new InvokeCommand('#custom_condition_step4', 'removeClass', ['hidden']));
    $response->addCommand(new ReplaceCommand('#field_setting_form', $step4));
    
    return $response;
  }

}
