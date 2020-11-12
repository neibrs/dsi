<?php

/**
 * Implements hook_views_data().
 */
function views_template_views_data() {
  $data['views']['views_template_conditions'] = [
    'title' => t('Views conditions'),
    'area' => [
      'id' => 'views_template_conditions',
    ],
  ];

  return $data;
}
