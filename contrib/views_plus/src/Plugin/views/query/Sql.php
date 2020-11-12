<?php

namespace Drupal\views_plus\Plugin\views\query;

use Drupal\Core\Database\Database;
use Drupal\views\Plugin\views\query\Sql as SqlBase;

class Sql extends SqlBase {

  public function query($get_count = FALSE) {
    // Check query distinct value.
    if (empty($this->noDistinct) && $this->distinct && !empty($this->fields)) {
      $base_field_alias = $this->addField($this->view->storage->get('base_table'), $this->view->storage->get('base_field'));
      $this->addGroupBy($base_field_alias);
      $distinct = TRUE;
    }

    /**
     * An optimized count query includes just the base field instead of all the fields.
     * Determine of this query qualifies by checking for a groupby or distinct.
     */
    if ($get_count && !$this->groupby) {
      foreach ($this->fields as $field) {
        if (!empty($field['distinct']) || !empty($field['function'])) {
          $this->getCountOptimized = FALSE;
          break;
        }
      }
    }
    else {
      $this->getCountOptimized = FALSE;
    }
    if (!isset($this->getCountOptimized)) {
      $this->getCountOptimized = TRUE;
    }

    $options = [];
    $target = 'default';
    $key = 'default';
    // Detect an external database and set the
    if (isset($this->view->base_database)) {
      $key = $this->view->base_database;
    }

    // Set the replica target if the replica option is set
    if (!empty($this->options['replica'])) {
      $target = 'replica';
    }

    // Go ahead and build the query.
    // db_select doesn't support to specify the key, so use getConnection directly.
    $query = Database::getConnection($target, $key)
      ->select($this->view->storage->get('base_table'), $this->view->storage->get('base_table'), $options)
      ->addTag('views')
      ->addTag('views_' . $this->view->storage->id());

    // Add the tags added to the view itself.
    foreach ($this->tags as $tag) {
      $query->addTag($tag);
    }

    if (!empty($distinct)) {
      $query->distinct();
    }

    // Add all the tables to the query via joins. We assume all LEFT joins.
    foreach ($this->tableQueue as $table) {
      if (is_object($table['join'])) {
        $table['join']->buildJoin($query, $table, $this);
      }
    }

    // Assemble the groupby clause, if any.
    $this->hasAggregate = FALSE;
    $non_aggregates = $this->getNonAggregates();
    if (count($this->having)) {
      $this->hasAggregate = TRUE;
    }
    elseif (!$this->hasAggregate) {
      // Allow 'GROUP BY' even no aggregation function has been set.
      $this->hasAggregate = $this->view->display_handler->getOption('group_by');
    }
    $groupby = [];
    if ($this->hasAggregate && (!empty($this->groupby) || !empty($non_aggregates))) {
      $groupby = array_unique(array_merge($this->groupby, $non_aggregates));
    }

    // Make sure each entity table has the base field added so that the
    // entities can be loaded.
    $entity_information = $this->getEntityTableInfo();
    if ($entity_information) {
      $params = [];
      if ($groupby) {
        // Handle grouping, by retrieving the minimum entity_id.
        $params = [
          'function' => 'min',
        ];
      }

      // 有 group_by 时，不乱加字段.
      if (!$this->displayHandler->useGroupBy()) {
        foreach ($entity_information as $entity_type_id => $info) {
          $entity_type = \Drupal::entityTypeManager()->getDefinition($info['entity_type']);
          $base_field = !$info['revision'] ? $entity_type->getKey('id') : $entity_type->getKey('revision');
          $this->addField($info['alias'], $base_field, '', $params);
        }
      }
    }

    // Add all fields to the query.
    $this->compileFields($query);

    // Add groupby.
    if ($groupby) {
      foreach ($groupby as $field) {
        // Handle group by of field without table alias to avoid ambiguous
        // column error.
        if ($field == $this->view->storage->get('base_field')) {
          $field = $this->view->storage->get('base_table') . '.' . $field;
        }
        $query->groupBy($field);
      }
      if (!empty($this->having) && $condition = $this->buildCondition('having')) {
        $query->havingCondition($condition);
      }
    }

    if (!$this->getCountOptimized) {
      // we only add the orderby if we're not counting.
      if ($this->orderby) {
        foreach ($this->orderby as $order) {
          if ($order['field'] == 'rand_') {
            $query->orderRandom();
          }
          else {
            $query->orderBy($order['field'], $order['direction']);
          }
        }
      }
    }

    if (!empty($this->where) && $condition = $this->buildCondition('where')) {
      $query->condition($condition);
    }

    // Add a query comment.
    if (!empty($this->options['query_comment'])) {
      $query->comment($this->options['query_comment']);
    }

    // Add the query tags.
    if (!empty($this->options['query_tags'])) {
      foreach ($this->options['query_tags'] as $tag) {
        $query->addTag($tag);
      }
    }

    // Add all query substitutions as metadata.
    $query->addMetaData('views_substitutions', \Drupal::moduleHandler()->invokeAll('views_query_substitutions', [$this->view]));

    return $query;
  }

}
