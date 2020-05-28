<?php

namespace Drupal\entity_filter;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
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

  public function fetchFields($base, $type, $grouping = FALSE, $sub_type = NULL) {
    // Get data table
    $base_tables[] = $base;
    if ($entity_type = $this->entityTypeManager->getDefinition($base, FALSE)) {
      $table = $entity_type->getDataTable() ?: $entity_type->getBaseTable();
      if (!in_array($table, $base_tables)) {
        $base_tables[] = $table;
      }
    }
    if ($predefined_filter = \Drupal::entityTypeManager()->getStorage('predefined_filter')->load($base)) {
      $relationships = $predefined_filter->getRelationships();
      foreach ($relationships as $relationship) {
        $table = $relationship['table'];
        if (!in_array($table, $base_tables)) {
          $base_tables[] = $table;
        }
        $info = $this->viewsData->get($relationship['table'])[$relationship['field']]['relationship'];
        $table = $info['base'];
        if (!in_array($table, $base_tables)) {
          $base_tables[] = $table;
        }
      }
    }

    return Views::viewsDataHelper()->fetchFields($base_tables, $type, $grouping, $sub_type);
  }
  
}
