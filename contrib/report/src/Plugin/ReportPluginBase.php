<?php

namespace Drupal\report\Plugin;

use Collator;
use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Serialization\Json;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Link;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginWithFormsInterface;
use Drupal\Core\Plugin\PluginWithFormsTrait;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\Core\Url;
use Drupal\report\Entity\ReportInterface;
use Drupal\views\Views;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for Report plugins.
 */
abstract class ReportPluginBase extends PluginBase implements ReportPluginInterface, PluginWithFormsInterface, ContainerFactoryPluginInterface {

  use PluginWithFormsTrait;
  use StringTranslationTrait;

  /**
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entity_type_manager) {
    // The $entityTypeManager must be set before setConfiguration is called.
    $this->entityTypeManager = $entity_type_manager;

    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'base_table' => '',
      'filters' => [],
      'arguments' => [],
      'style' => 'bar',
      'field' => [],
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration() {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration) {
    $this->configuration = NestedArray::mergeDeep(
      $this->defaultConfiguration(),
      $configuration
    );

    if ($style = \Drupal::request()->get('style')) {
      $this->configuration['style'] = $style;
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $table_settings = \Drupal::config('report.settings')->get('base_tables');
    
    // base_table
    $options = [];
    $views_data = Views::viewsData();
    $base_tables = array_keys($views_data->fetchBaseTables());
    foreach ($base_tables as $table) {
      if (!in_array($table, $table_settings)) {
        continue;
      }
      
      $views_info = $views_data->get($table);
      $options[$table] = $views_info['table']['base']['title'];
    }
    
    // 拼音排序
    (new Collator('zh-CN'))->asort($options);
    
    $form['base_table'] = [
      '#type' => 'select',
      '#title' => $this->t('Base table'),
      '#description' => $this->t('Data source of the report.'),
      '#required' => TRUE,
      '#options' => $options,
      '#default_value' => $this->configuration['base_table'],
      '#ajax' => [
        'callback' => '\Drupal\report\Plugin\ReportPluginBase::baseTableSwitch',
        'wrapper' => 'statistics-field-wrapper'
      ],
    ];
    
    // statistics_field
    $base_table = $this->configuration['base_table'];
    $user_input = $form_state->getUserInput();
    if (isset($user_input['settings'])) {
      $base_table = $user_input['settings']['base_table'];
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
    
    $statistics_field = 'none';
    if (isset($this->configuration['field']['field'])) {
      $statistics_field = $this->configuration['field']['table'] . '.' . $this->configuration['field']['field'];
    }
    $form['field']['statistics_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Statistics field'),
      '#description' =>$this->t('Report statistical results use statistical fields as data.'),
      '#options' => $options,
      '#default_value' => $statistics_field,
      '#ajax' => [
        'callback' => '\Drupal\report\Plugin\ReportPluginBase::statisticsFieldSwitch',
        'wrapper' => 'statistics-relationship-wrapper'
      ],
      '#prefix' => '<div id="statistics-field-wrapper">',
      '#suffix' => '</div>',
    ];
    
    //statistics_relationship
    $user_input = $form_state->getUserInput();
    if (isset($user_input['settings'])) {
      $statistics_field = $user_input['settings']['field']['statistics_field'];
    }
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
    $form['field']['statistics_relationship'] = [
      '#type' => 'select',
      '#title' => $this->t('Statistics relationship'),
      '#description' => $this->t('The relationship between the base table and the statistical field.'),
      '#options' => $relationship_options,
      '#default_value' => isset($this->configuration['field']['relationship']) ? $this->configuration['field']['relationship'] : 'none' ,
      '#prefix' => '<div id="statistics-relationship-wrapper">',
      '#suffix' => '</div>',
    ];
  
    $form['field']['statistics_method'] = [
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
      '#default_value' => isset($this->configuration['field']['group_type']) ? $this->configuration['field']['group_type'] : 'count',
    ];
    
    $form['style'] = [
      '#type' => 'select',
      '#title' => $this->t('Style'),
      '#options' => array_map(function ($item) {
        return $item['label'];
      }, $this->getStyles()),
      '#default_value' => $this->configuration['style'],
    ];

    $report = $form_state->getBuildInfo()['callback_object']->getEntity();
    if (!$report->isNew()) {
      $form['filters'] = \Drupal::service('entity_filter.manager')
        ->buildFiltersDisplayForm($this->configuration['filters'], Url::fromRoute('entity.report.filters_form', [
          'report' => $report->id(),
        ], [
          'query' => \Drupal::destination()->getAsArray(),
        ]));
    }

    // TODO arguments

    return $form;
  }


  protected function getStyles() {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Process the submission handling if no errors occurred only.
    if (!$form_state->getErrors()) {
      $this->configuration['base_table'] = $form_state->getValue('base_table');
      $this->configuration['style'] = $form_state->getValue('style');
      
      $statistics_field = $form_state->getValue(['field', 'statistics_field']);
      if (empty($statistics_field) || $statistics_field == 'none') {
        $statistics_field = $this->configuration['base_table'] . '.id';
      }
      list($statistics_table, $statistics_field) = explode('.', $statistics_field);
      $field = [
        'id' => $statistics_field,
        'table' => $statistics_table,
        'field' => $statistics_field,
        'relationship' => $form_state->getValue(['field', 'statistics_relationship']),
        'group_type' => $form_state->getValue(['field', 'statistics_method']),
        'plugin_id' => 'standard',
      ];
      $this->configuration['field'] = $field;
    }
  }

  /**
   * Gather data from database.
   *
   * @return mixed
   */
  protected function gatherData($filters, $field = NULL) {
    $base_table = $this->configuration['base_table'];
  
    if (empty($field)) {
      $field = $this->configuration['field'];
    }
    $fields[] = $field;
  
    // Create the view executable.
    /** @var \Drupal\views\ViewExecutable $view_executable */
    $view_executable = \Drupal::service('entity_filter.manager')->createView($base_table, ['filters' => $filters, 'fields' => $fields]);
  
    $default = $view_executable->getDisplay();
    $default->options['group_by'] = empty($this->configuration['field']['group_type']) ? FALSE : TRUE;
  
    // Build arguments.
    $request = \Drupal::request();
    $arguments = [];
    $views_args = [];
    foreach ($this->configuration['arguments'] as $id => $argument) {
      if ($arg = $request->get($id)) {
        $arguments[$id] = $argument;
        $views_args[] = $arg;

        // Fix Undefined index: id
        $arguments[$id]['id'] = $id;
      }
    }
    $default->options['arguments'] = $arguments;
  
    if (!empty($views_args)) {
      $view_executable->setArguments($views_args);
    }
  
    // Calculate data.
    $view_executable->build('default');
    $query = $view_executable->build_info['query'];
    $query->preExecute();
    $result = $query->execute()->fetchField();
    
    if ($this->configuration['field']['group_type'] != 'count') {
      $result = round($result,2);
    }
    return $result;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ReportInterface $entity, $view_mode = 'full') {
    $this->entity = $entity;
    
    $style = $this->configuration['style'];
    $build = [
      '#theme' => 'box',
      '#title' => $entity->label(),
      '#body' => $this->$style($entity),
      '#attributes' => [
        'class' => ['report-to-print'],
      ],
      '#attached' => [
        'library' => ['report/report_to_print'],
      ]
    ];

    $styles = $this->getStyles();
    if (count($styles) > 1) {
      // Some plugin such as OrganizationStatistics need additional parameters.
      $route_parameter = \Drupal::routeMatch()->getRawParameters()->all();

      foreach ($styles as $id => $style) {
        $build['#tools'][$id] = [
          '#type' => 'html_tag',
          '#tag' => 'a',
          '#value' => '<i class="fa ' . $style['icon'] . '"></i>',
          '#attributes' => [
            'href' => Url::fromRoute('report.ajax_style', [
              'report' => $entity->id(),
              'style' => $id,
            ] + $route_parameter)->toString(),
            'class' => ['use-ajax'],
          ],
        ];
      }
    }

    // Build the conditions.
    $filters = $entity->getFiltersOverride();
    $build['#footer'] = \Drupal::service('entity_filter.manager')
      ->buildFiltersDisplayForm($filters, Url::fromRoute('entity.report.filters_override_form', [
        'report' => $entity->id(),
      ], [
        'query' => \Drupal::destination()->getAsArray(),
      ]));

    return $build;
  }
  
  public function buildFiltersDisplayForm($filters, Url $url) {
    $build = [
      '#type' => 'container',
    ];
    
    $build['label'] = [
      '#type' => 'label',
      '#title' => t('Conditions'),
      // Fix Notice: Undefined index: #title_display
      '#title_display' => '',
    ];
    
    if ($filters) {
      $labels = [];
      foreach ($filters as $filter) {
        $this->getFilterLabels($filter, $labels);
      }
      
      $build['filters'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => implode(', ', $labels),
      ];
    }
    
    $build['action'] = [
      '#type' => 'link',
      '#title' => t('Edit'),
      '#url' => $url,
      '#attributes' => [
        'class' => ['use-ajax', 'button', 'button--small'],
        'data-dialog-type' => 'modal',
        'data-dialog-options' => Json::encode([
          'width' => 700,
        ]),
      ],
    ];
    
    return $build;
  }
  
  public function getFilterLabels($filter, &$labels, $label = NULL) {
    $label = $label . $filter['filter']['admin_label'];
    if (isset($filter['subtree'])) {
      $label = $label . ':';
      foreach ($filter['subtree'] as $sub_filter) {
        $this->getFilterLabels($sub_filter, $labels, $label);
      }
    }
    else {
      $labels[] = $label;
    }
  }
   /**
   * Helper function for get cache tags.
   */
  protected function getCacheTags() {
    $cache_tags = [];

    // 获取基表的缓存标记
    $base_tables = $this->getBaseTables();
    foreach ($base_tables as $base_table) {
      $cache_tags = $this->getCacheTagsByBaseTable($base_table, $cache_tags);

      // 获取关系插件的缓存标记.
      $relationships = \Drupal::service('entity_filter.manager')->fetchRelationships($base_table);
      foreach ($relationships as $key => $relationship) {
        list($relationship_table, $relationship_field) = explode('.', $key);
        // If this relationship is valid for this type, add it to the list.
        $data = Views::viewsData()->get($relationship_table);
        if (isset($data[$relationship_field]['relationship']['base']) && $base = $data[$relationship_field]['relationship']['base']) {
          $cache_tags = $this->getCacheTagsByBaseTable($base, $cache_tags);
        }
      }
    }

    return $cache_tags;
  }

  private function getCacheTagsByBaseTable($table, $cache_tags) {
    $views_data = Views::viewsData()->get($table);
    if (isset($views_data['table']['entity type'])) {
      $entity_type_id = $views_data['table']['entity type'];
      $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);
      $cache_tags = Cache::mergeTags($cache_tags, $entity_type->getListCacheTags());
    }

    return $cache_tags;
  }

  protected function getBaseTables() {
    $base_table = $this->configuration['base_table'];
    $base_tables[$base_table] = $base_table;

    // Conditions
    foreach ($this->configuration['filters'] as $filter) {
      $base_table = $filter['table'];
      if (!isset($base_tables[$base_table])) {
        $base_tables[$base_table] = $base_table;
      }
    }

    return $base_tables;
  }

  /**
   * Callback for AJAX.
   */
  public function baseTableSwitch($form, FormStateInterface $form_state) {
    return $form['settings']['field']['statistics_field'];
  }


  /**
   * Callback for AJAX.
   */
  public function statisticsFieldSwitch($form, FormStateInterface $form_state) {
    return $form['settings']['field']['statistics_relationship'];
  }
}
