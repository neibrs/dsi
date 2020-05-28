<?php

namespace Drupal\report\Plugin;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Component\Utility\NestedArray;
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
      'statistics_method' => 'count',
      'statistics_field' => '',
      'statistics_relationship' =>'',
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
    $options = [];
    $views_data = Views::viewsData();
    $base_tables = array_keys($views_data->fetchBaseTables());
    foreach ($base_tables as $table) {
      $views_info = $views_data->get($table);
      $options[$table] = $views_info['table']['base']['title'];
    }
    $form['base_table'] = [
      '#type' => 'select',
      '#title' => $this->t('Base table'),
      '#options' => $options,
      '#default_value' => $this->configuration['base_table'],
      '#ajax' => [
        'callback' => '\Drupal\report\Plugin\ReportPluginBase::baseTableSwitch',
        'wrapper' => 'statistics-field-wrapper'
      ],
    ];
    
    $form['statistics_method'] = [
      '#type' => 'select',
      '#title' => $this->t('Statistics method'),
      '#options' => [
        'count' => $this->t('Count'),
        'sum' => $this->t('Sum'),
        'avg' => $this->t('Average'),
      ],
      '#default_value' => $this->configuration['statistics_method'],
    ];
    
  
    $base_table = $this->configuration['base_table'];
    $user_input = $form_state->getUserInput();
    if (isset($user_input['settings'])) {
      $base_table = $user_input['settings']['base_table'];
    }
    $fields = \Drupal::service('entity_filter.manager')->fetchFields($base_table, 'field', FALSE, 'field');
    $options = ['' => '-Select-'];
    $options += array_map(function ($item) {
      return '(' . $item['group'] . ') ' . $item['title'];
    }, $fields);
    $form['statistics_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Statistics field'),
      '#options' => $options,
      '#default_value' => $this->configuration['statistics_field'],
      '#states' => [
        'invisible' => [
          'select[name="settings[statistics_method]"]' => ['value' => 'count'],
        ],
      ],
      '#ajax' => [
        'callback' => '\Drupal\report\Plugin\ReportPluginBase::statisticsFieldSwitch',
        'wrapper' => 'statistics-relationship-wrapper'
      ],
      '#prefix' => '<div id="statistics-field-wrapper">',
      '#suffix' => '</div>',
    ];
    
    //statistics_relationship
    $statistics_field = $this->configuration['statistics_field'];
    $user_input = $form_state->getUserInput();
    if (isset($user_input['settings'])) {
      $statistics_field = $user_input['settings']['statistics_field'];
    }
    $relationship_options = ['' => '-Select-'];
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
    $form['statistics_relationship'] = [
      '#type' => 'select',
      '#title' => $this->t('Statistics relationship'),
      '#options' => $relationship_options,
      '#default_value' => $this->configuration['statistics_relationship'],
      '#states' => [
        'invisible' => [
          'select[name="settings[statistics_method]"]' => ['value' => 'count'],
        ],
      ],
      '#prefix' => '<div id="statistics-relationship-wrapper">',
      '#suffix' => '</div>',
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
      $this->configuration['statistics_method'] = $form_state->getValue('statistics_method');
      $this->configuration['statistics_field'] = $form_state->getValue('statistics_field');
      $this->configuration['statistics_relationship'] = $form_state->getValue('statistics_relationship');
    }
  }

  /**
   * Gather data from database.
   *
   * @return mixed
   */
  protected function gatherData($filters) {
    $base_table = $this->configuration['base_table'];
  
    // Build relationships.
    $entity_type_id = $base_table;
    $views_data = Views::viewsData()->get($base_table);
    if (isset($views_data['table']['entity type'])) {
      $entity_type_id = $views_data['table']['entity type'];
    }
    $predefined_filter = $this->entityTypeManager->getStorage('predefined_filter')->load($entity_type_id);
    $predefined_relationships = $predefined_filter->getRelationships();
    $relationships = [];
    foreach ($filters as $id => $filter) {
      if (isset($filter['relationship'])) {
        $relationship = $filter['relationship'];
        if (!isset($relationships[$relationship])) {
          // 处理嵌套关系.
          if (isset($predefined_relationships[$relationship]['relationship'])) {
            $relationship_relationship = $predefined_relationships[$relationship]['relationship'];
            if (!isset($relationships[$relationship_relationship])) {
              $relationships[$relationship_relationship] = $predefined_relationships[$relationship_relationship];
              // Fix Undefined index: id
              $relationships[$relationship_relationship]['id'] = $relationship_relationship;
            }
          }
        
          $relationships[$relationship] = $predefined_relationships[$relationship];
          // Fix Undefined index: id
          $relationships[$relationship]['id'] = $relationship;
        }
      }
    
      // Fix Undefined index: id
      $filters[$id]['id'] = $id;
    }
  
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
  
    // Statistics method.
    if ($this->configuration['statistics_method'] != 'count') {
      $database = \Drupal::database();
      $query = $database->select($base_table);
      
      // Join relationship table.
      if ($predefined_filter = \Drupal::entityTypeManager()->getStorage('predefined_filter')->load($base_table)) {
        $relationships = $predefined_filter->getRelationships();
        foreach ($relationships as $relationship) {
          $info = Views::viewsData()->get($relationship['table'])[$relationship['field']]['relationship'];
          $table = $info['base'];
          if ($table == $base_table) {
            continue;
          }
          $base_tables[] = $table;
          $query->addJoin('LEFT', $table, NULL, $table . '.id =' . $relationship['table'] . '.' . $relationship['field']);
        }
      }
      
      //Add conditions.
      foreach ($filters as $filter) {
        $query->condition($filter['table'] . '.' . $filter['field'], $filter['value'], $filter['operator']);
      }
      
      $query->addExpression($this->configuration['statistics_method'] . '(' . $this->configuration['statistics_field'] . ')');
      $count = $query->execute()->fetchField();
      if (empty($count)) {
        $count = 0;
      }
      return $count;
    }
  
    // Create the view executable.
    $configuration = [
      'base_table' => $base_table,
      'display' => [
        'default' => [
          'display_options' => [
            'filters' => $filters,
            'relationships' => $relationships,
            'arguments' => $arguments,
          ],
          'display_plugin' => 'default',
          'id' => 'default',
        ],
      ],
    ];
  
    $view = $this->entityTypeManager->getStorage('view')->create($configuration);
    $view = Views::executableFactory()->get($view);
    if (!empty($views_args)) {
      $view->setArguments($views_args);
    }
  
    // Calculate data count.
    $view->build('default');
    $count_query = $view->build_info['count_query'];
    $count_query->preExecute();
    $count_query = $count_query->countQuery();
    $count = $count_query->execute()->fetchField();
  
    return $count;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ReportInterface $entity, $view_mode = 'full') {
    $style = $this->configuration['style'];
    $build = [
      '#theme' => 'box',
      '#title' => $entity->label(),
      '#body' => $this->$style($entity),
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

  /**
   * Helper function for get cache tags.
   */
  protected function getCacheTags() {
    $cache_tags = [];

    $views_data = Views::viewsData()->get($this->configuration['base_table']);
    if (isset($views_data['table']['entity type'])) {
      $entity_type_id = $views_data['table']['entity type'];
      $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);
      $cache_tags = $entity_type->getListCacheTags();
    }

    return $cache_tags;
  }
  
  /**
   * Callback for AJAX.
   */
  public function baseTableSwitch($form, FormStateInterface $form_state) {
    return $form['settings']['statistics_field'];
  }


  /**
   * Callback for AJAX.
   */
  public function statisticsFieldSwitch($form, FormStateInterface $form_state) {
    return $form['settings']['statistics_relationship'];
  }
}
