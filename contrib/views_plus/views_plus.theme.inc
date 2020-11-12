<?php

/**
 * @file
 */

use Drupal\Core\Render\Element;

/**
 * Implements hook_preprocess_HOOK() template for views_plus_style_plugin_table_tree.
 */
function template_preprocess_views_plus_style_plugin_table_tree(&$variables) {
  $form = $variables['form'];

  $header = [
    t('Field'),
    t('Column'),
    t('Align'),
    t('Separator'),
    t('Tree column'),
    [
      'data' => t('Sortable'),
      'align' => 'center',
    ],
    [
      'data' => t('Default order'),
      'align' => 'center',
    ],
    [
      'data' => t('Default sort'),
      'align' => 'center',
    ],
    [
      'data' => t('Hide empty column'),
      'align' => 'center',
    ],
    [
      'data' => t('Responsive'),
      'align' => 'center',
    ],
  ];
  $rows = [];
  foreach (Element::children($form['columns']) as $id) {
    $row = [];
    $row[]['data'] = $form['info'][$id]['name'];
    $row[]['data'] = $form['columns'][$id];
    $row[]['data'] = $form['info'][$id]['align'];
    $row[]['data'] = $form['info'][$id]['separator'];

    $row[] = [
      'data' => $form['info'][$id]['tree_column'],
      'align' => 'center',
    ];

    if (!empty($form['info'][$id]['sortable'])) {
      $row[] = [
        'data' => $form['info'][$id]['sortable'],
        'align' => 'center',
      ];
      $row[] = [
        'data' => $form['info'][$id]['default_sort_order'],
        'align' => 'center',
      ];
      $row[] = [
        'data' => $form['default'][$id],
        'align' => 'center',
      ];
    }
    else {
      $row[] = '';
      $row[] = '';
      $row[] = '';
    }
    $row[] = [
      'data' => $form['info'][$id]['empty_column'],
      'align' => 'center',
    ];
    $row[] = [
      'data' => $form['info'][$id]['responsive'],
      'align' => 'center',
    ];
    $rows[] = $row;
  }

  // Add the special 'None' row.
  $rows[] = [['data' => t('None'), 'colspan' => 7], ['align' => 'center', 'data' => $form['default'][-1]], ['colspan' => 2]];

  // Unset elements from the form array that are used to build the table so that
  // they are not rendered twice.
  unset($form['default']);
  unset($form['info']);
  unset($form['columns']);

  $variables['table'] = [
    '#type' => 'table',
    '#theme' => 'table__views_ui_style_plugin_table',
    '#header' => $header,
    '#rows' => $rows,
  ];
  $variables['form'] = $form;
}
