<?php

namespace Drupal\entity_filter;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\views\Plugin\views\display\DisplayPluginInterface;
use Drupal\views\Views;
use Drupal\views\ViewsData;

class EntityFilterManager implements EntityFilterManagerInterface {

  /**
   * The entity type manager.
   *
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
      $items = array_map(function ($filter) {
        return $filter['admin_label'];
      }, $filters);
      $build['filters'] = [
        '#type' => 'html_tag',
        '#tag' => 'span',
        '#value' => implode(', ', $items),
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

  /**
   * Fetches a list of all fields available for a given base type.
   *
   * @param array|string $base
   *   A list or a single base_table, for example node.
   * @param string $type
   *   The handler type, for example field or filter.
   * @param bool $grouping
   *   Should the result grouping by its 'group' label.
   * @param string $sub_type
   *   An optional sub type. E.g. Allows making an area plugin available for
   *   header only, instead of header, footer, and empty regions.
   *
   * @return array
   *   A keyed array of in the form of 'base_table' => 'Description'.
   */
  public function fetchFields($base, $type, $grouping = FALSE, $sub_type = NULL) {
    // Get data table
    $base_tables[] = $base;
    if ($entity_type = $this->entityTypeManager->getDefinition($base, FALSE)) {
      $table = $entity_type->getDataTable() ?: $entity_type->getBaseTable();
      if (!in_array($table, $base_tables)) {
        $base_tables[] = $table;
      }
    }

    // 将所有关系的表添加到 $base_tables
    $relationships = $this->fetchRelationships($base);
    foreach ($relationships as $key => $relationship) {
      if (!in_array($relationship['base'], $base_tables)) {
        $base_tables[] = $relationship['base'];
      }
    }

    return Views::viewsDataHelper()->fetchFields($base_tables, $type, $grouping, $sub_type);
  }

  public function fetchRelationships($base) {
    // Get data table
    $base_tables[] = $base;
    if ($entity_type = $this->entityTypeManager->getDefinition($base, FALSE)) {
      $table = $entity_type->getDataTable() ?: $entity_type->getBaseTable();
      if (!in_array($table, $base_tables)) {
        $base_tables[] = $table;
      }
    }

    // 需要补充 predefined_filter 定义的关系。
    $predefined_filter = \Drupal::entityTypeManager()->getStorage('predefined_filter')->load($base);
    if ($predefined_filter) {
      $predefined_relationships = $predefined_filter->getRelationships();
      foreach ($predefined_relationships as $relationship_id => $relationship) {
        if (!isset($base_tables[$relationship['table']]) && !in_array($relationship['table'], $base_tables)) {
          $base_tables[] = $relationship['table'];
        }
      }
    }

    $relationships = Views::viewsDataHelper()->fetchFields($base_tables, 'relationship', FALSE, 'relationship');

    // 只能支持 predefined_filter 有的 relationships.
    foreach ($relationships as $key => $relationship) {
      list($t, $r) = explode('.', $key);
      if (isset($predefined_relationships) && !in_array($r, array_keys($predefined_relationships))) {
        unset($relationships[$key]);
      }
    }

    return $relationships;
  }

  public function getReadableFiltersString($base_table, $filters) {
    if (empty($filters)) {
      return '';
    }

    $output = [];
    $view = \Drupal::entityTypeManager()->getStorage('view')->create([
      'base_table' => $base_table,
    ]);
    $view_executable = Views::executableFactory()->get($view);
    $display = $view_executable->getDisplay();
    $this->addHandlersRelationshipToDisplay($display, $filters);
    foreach ($filters as $filter) {
      $handler = \Drupal::service('plugin.manager.views.filter')->getHandler($filter);
      $handler->init($view_executable, $display, $filter);
  
      // 如果 admin_label 不为空，则直接获取 admin_label (条件描述字符串在生成条件时已存入 filter 的 admin_label).
      // @see Drupal\entity_filter\Form\FiltersFormBase::adminLabel()
      if (isset($filter['admin_label']) && !empty($filter['admin_label'])) {
        $admin_label = $handler->adminLabel(TRUE);
      }
      else {
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
      }
      
      $output[] = $admin_label;
    }

    return implode(', ', $output);
  }
  
  /**
   * {@inheritdoc}
   */
  public function addHandlersRelationshipToDisplay(DisplayPluginInterface $display, $handlers) {
    $predefined_relationships = [];
    $base_table = $display->view->storage->get('base_table');
    if ($predefined_filter = \Drupal::entityTypeManager()->getStorage('predefined_filter')->load($base_table)) {
      $predefined_relationships = $predefined_filter->getRelationships();
    }
    
    foreach ($handlers as $handler) {
      if (isset($handler['relationship'])) {
        $this->addRelationshipToDisplay($base_table, $display, $handler['relationship'], $predefined_relationships);
      }
    }
  }
  
  protected function addRelationshipToDisplay($base_table, $display, $relationship, $predefined_relationships) {
    if (in_array($relationship, $display->options['relationships'])) {
      return;
    }
  
    if (isset($predefined_relationships[$relationship])) {
      // 如果关系存在关系，需要先添加关系的关系
      if (isset($predefined_relationships[$relationship]['relationship'])) {
        $this->addRelationshipToDisplay($base_table, $display, $predefined_relationships[$relationship]['relationship'], $predefined_relationships);
      }
    
      $display->options['relationships'][$relationship] = $predefined_relationships[$relationship];
      $display->options['relationships'][$relationship]['id'] = $relationship; // Fix Undefined index: id
    }
    else {
      $relationship_fields = Views::viewsDataHelper()->fetchFields($base_table, 'relationship');
      foreach ($relationship_fields as $key => $relationship_field) {
        list($relationship_table, $relationship_field) = explode('.', $key);
        if ($relationship_field == $relationship) {
          $display->options['relationships'][$relationship] = [
            'id' => $relationship, // Fix Undefined index: id
            'table' => $relationship_table,
            'field' => $relationship,
          ];
        }
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function createView($base_table, $handlers_config) {
    $configuration = [
      'base_table' => $base_table,
      'display' => [
        'default' => [
          'display_options' => [
            'filters' => isset($handlers_config['filters']) ? $handlers_config['filters'] : [],
            'fields' => isset($handlers_config['fields']) ? $handlers_config['fields'] : [],
          ],
          'display_plugin' => 'default',
          'id' => 'default',
        ],
      ],
    ];
    $view = $this->entityTypeManager->getStorage('view')->create($configuration);
    $view_executable = Views::executableFactory()->get($view);
    $default = $view_executable->getDisplay();
  
    // 将 filters 用到的 relationship 添加到 display options 里.
    if (isset($handlers_config['filters'])) {
      \Drupal::service('entity_filter.manager')->addHandlersRelationshipToDisplay($default, $handlers_config['filters']);
    }
  
    // 将 fields 用到的 relationship 添加到 display options 里.
    if (isset($handlers_config['fields'])) {
      \Drupal::service('entity_filter.manager')->addHandlersRelationshipToDisplay($default, $handlers_config['fields']);
    }
  
    return $view_executable;
  }

}
