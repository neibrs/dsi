<?php

namespace Drupal\entity_filter\Form;

use Drupal\Component\Serialization\Json;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
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
    
    $views_data = Views::viewsData()->get($base_table);
    if (isset($views_data['table']['entity type'])) {
      $base_table = $views_data['table']['entity type'];
    }
  
    $form['#theme'] = 'entity_filter_form';
    $form['#attached']['library'][] = 'entity_filter/filters_form';

    $form['predefined_filters'] = $this->buildPredefinedFilters($base_table);

    $form['custom_condition_builder'] = $this->buildCustomConditionBuilder($base_table);

    $form['filters'] = $this->buildFilters($filters);

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => t('Save'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * Build the predefined filters selection tree.
   *
   * @return array
   *   The render array.
   */
  protected function buildPredefinedFilters($base_table) {
    $build = [
      '#theme' => 'tree',
      '#items' => [],
    ];

    /** @var \Drupal\entity_filter\Entity\PredefinedFilterInterface $predefined_filter */
    $predefined_filter = $this->entityTypeManager->getStorage('predefined_filter')->load($base_table);
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
      $relationship_data = $this->viewsData->get($views_relationship['base']);
      $relationship_entity_type = $this->entityTypeManager->getDefinition($relationship_data['table']['entity type']);

      /** @var \Drupal\entity_filter\Entity\PredefinedFilterInterface $predefined_filter */
      $predefined_filter = $this->entityTypeManager->getStorage('predefined_filter')->load($relationship_entity_type->id());
      if (!$predefined_filter) {
        continue;
      }

      $build['#items'][$id] = [
        'title' => $views_relationship['label'],
        'below' => [],
        'is_expanded' => TRUE,
        'attributes' => new Attribute(),
      ];

      $filters = $predefined_filter->getFilters();
      $this->buildFiltersSelector($build, $filters, $id);
    }

    $build['#attached']['library'][] = 'eabax_core/tree';

    return $build;
  }

  protected function buildFiltersSelector(&$build, $filters, $relationship = NULL) {
    foreach ($filters as $id => $filter) {
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
  protected function buildCustomConditionBuilder($base_table) {
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
        'id' => ['add_custom_condition'],
      ],
    ];

    $form['custom_condition']['step2'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'custom_condition_step2'],
    ];

    $filters = \Drupal::service('entity_filter.manager')->fetchFields($base_table, 'filter', FALSE, 'filter');
    $form['custom_condition']['step2']['item'] = [
      '#type' => 'table',
      '#header' => [
        $this->t('Title'),
        $this->t('Category'),
      ],
    ];
    foreach ($filters as $key => $filter) {
      $form['custom_condition']['step2']['item'][$key]['title'] = [
        '#type' => 'link',
        '#title' => $filter['title'],
        '#url' => Url::fromRoute('entity_filter.views_handler_config', [
          'base_table' => $base_table,
          'handler_type' => 'filter',
          'key' => $key,
        ]),
        '#attributes' => ['class' => ['use-ajax']],
      ];
      $form['custom_condition']['step2']['item'][$key]['category'] = [
        '#markup' => $filter['group'],
      ];
    }

    $form['custom_condition']['step3'] = [
      '#type' => 'container',
      '#attributes' => ['id' => 'custom_condition_step3'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  protected function buildFilters($filters) {
    $build= [
      '#type' => 'table',
      '#header' => [$this->t('Item'), ''],
      '#attributes' => [
        'data-theme' => 'entity_filter',
        'id' => 'predefined_filters_item'
      ],
    ];
    foreach ($filters as $id => $filter) {
      $build[$id]['#attributes']['data-id'] = $id;

      $build[$id]['admin_label'] = [
        '#markup' => $filter['admin_label'],
      ];
      $build[$id]['delete'] = [
        '#markup' => '<i class="fa fa-remove" />',
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
