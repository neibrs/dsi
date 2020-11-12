<?php

namespace Drupal\views_plus;

use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\views\EntityViewsData as EntityViewsDataBase;

class EntityViewsData extends EntityViewsDataBase {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // 对有效期的处理.
    $table = $this->entityType->getDataTable() ?: $this->entityType->getBaseTable();
    if (isset($data[$table]['effective_dates__value'])) {
      // 对有效期提供更好的 filter.
      $data[$table]['effective_dates__value']['filter']['id'] = 'effective_dates';
      // effective_dates 插件会判断 value 和 end_value，所以删除 effective_dates__end_value filter.
      unset($data[$table]['effective_dates__end_value']['filter']);

      // effective_dates : value 视图字段插件包含了 end_value ,可删除 end_value 的视图字段插件.
      unset($data[$table]['effective_dates__end_value']['field']);

      $data[$table]['effective_dates__start'] = [
        'title' => $this->t('Effective dates start'),
        'help' => $this->t('Filter the view to effective dates start'),
        'filter' => [
          'id' => 'datetime',
          'real field' => 'effective_dates__value',
          'field_name' => 'effective_dates',
          'allow empty' => TRUE,
        ],
        'field' => [
          'id' => 'date',
          'real field' => 'effective_dates__value',
        ],
      ];
      $data[$table]['effective_dates__end'] = [
        'title' => $this->t('Effective dates end'),
        'help' => $this->t('Filter the view to effective dates end'),
        'filter' => [
          'id' => 'datetime',
          'real field' => 'effective_dates__end_value',
          'field_name' => 'effective_dates',
          'allow empty' => TRUE,
        ],
        'field' => [
          'id' => 'date',
          'real field' => 'effective_dates__end_value',
        ],
      ];
    }

    return $data;
  }

  protected function mapSingleFieldViewsData($table, $field_name, $field_type, $column_name, $column_type, $first, FieldDefinitionInterface $field_definition) {
    $views_field = parent::mapSingleFieldViewsData($table, $field_name, $field_type, $column_name, $column_type, $first, $field_definition);

    switch ($field_type) {
      case 'daterange':
      case 'datetime':
        $views_field['field']['id'] = 'field';
        $views_field['argument']['id'] = 'datetime';
        $views_field['filter']['id'] = 'datetime';
        $views_field['sort']['id'] = 'datetime';
        $views_field['filter']['field_name'] = $field_name;
        break;

      default:
        switch ($column_type) {
          case 'numeric':
            $views_field['field']['id'] = 'field';
            $views_field['argument']['id'] = 'numeric';
            $views_field['filter']['id'] = 'numeric';
            $views_field['sort']['id'] = 'standard';
            break;
        }
    }

    return $views_field;
  }

}
