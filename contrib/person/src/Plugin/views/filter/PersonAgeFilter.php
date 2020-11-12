<?php

namespace Drupal\person\Plugin\views\filter;

use Drupal\views\Plugin\views\filter\NumericFilter;

/**
 * @ViewsFilter("person_age")
 */
class PersonAgeFilter extends NumericFilter {

  /**
   * {@inheritdoc}
   */
  public function query() {
    $this->ensureMyTable();
    $field = "YEAR(CURDATE())-LEFT($this->tableAlias.$this->realField,4)";

    $info = $this->operators();
    if (!empty($info[$this->operator]['method'])) {
      $this->{$info[$this->operator]['method']}($field);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function opBetween($field) {
    // Override base method, using addWhereExpression instead of addWhere.
    if (is_numeric($this->value['min']) && is_numeric($this->value['max'])) {
      $operator = $this->operator == 'between' ? 'BETWEEN' : 'NOT BETWEEN';
      $this->query->addWhereExpression($this->options['group'], $field . ' ' . $operator . ' ' . $this->value['min'] . ' AND ' . $this->value['max']);
    }
    elseif (is_numeric($this->value['min'])) {
      $operator = $this->operator == 'between' ? '>=' : '<';
      $this->query->addWhereExpression($this->options['group'], $field . ' ' . $operator . ' ' . $this->value['min']);
    }
    elseif (is_numeric($this->value['max'])) {
      $operator = $this->operator == 'between' ? '<=' : '>';
      $this->query->addWhereExpression($this->options['group'], $field . ' ' . $operator . ' ' . $this->value['max']);
    }
  }

  /**
   * {@inheritdoc}
   */
  protected function opSimple($field) {
    if (is_numeric($this->value['value'])) {
      $this->query->addWhereExpression($this->options['group'], $field . ' ' . $this->operator. ' ' . $this->value['value']);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function operators() {
    $operators = parent::operators();

    // 该插件不支持正则表达式匹配该字段$field = "YEAR(CURDATE())-LEFT($this->tableAlias.$this->realField,4)"
    unset($operators['regular_expression']);

    return $operators;
  }
}

