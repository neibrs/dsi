<?php

namespace Drupal\views_plus\Plugin\views\join;

use Drupal\views\Plugin\views\join\Subquery as SubqueryBase;

class Subquery extends SubqueryBase {

  public function buildJoin($select_query, $table, $view_query) {
    $this->left_query = str_replace('[leftTable]', $this->leftTable, $this->left_query);

    parent::buildJoin($select_query, $table, $view_query);
  }

}
