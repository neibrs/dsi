<?php

namespace Drupal\views_plus\Plugin\views\filter;

use Drupal\Component\Datetime\DateTimePlus;
use Drupal\Core\Form\FormStateInterface;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\datetime\Plugin\views\filter\Date;

/**
 * @ViewsFilter("effective_dates")
 */
class EffectiveDatesFilter extends Date {

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    parent::valueForm($form, $form_state);

    if (isset($form['value']['value'])) {
      $form['value']['value']['#type'] = 'date';
    }

    if (isset($form['value']['min'])) {
      $form['value']['min']['#type'] = 'date';
    }

    if (isset($form['value']['max'])) {
      $form['value']['max']['#type'] = 'date';
    }
  }

  /**
   * {@inheritdoc}
   */
  public function operators() {
    $operators = parent::operators();

    // 删除正则表达式.
    unset($operators['regular_expression']);

    return $operators;
  }

  /**
   * {@inheritdoc}
   */
  protected function opBetween($field) {
    $timezone = $this->getTimezone();
    $origin_offset = $this->getOffset($this->value['min'], $timezone);

    // Although both 'min' and 'max' values are required, default empty 'min'
    // value as UNIX timestamp 0.
    $min = (!empty($this->value['min'])) ? $this->value['min'] : '@0';

    // Convert to ISO format and format for query. UTC timezone is used since
    // dates are stored in UTC.
    $a = new DateTimePlus($min, new \DateTimeZone($timezone));
    $a = $this->query->getDateFormat($this->query->getDateField("'" . $this->dateFormatter->format($a->getTimestamp() + $origin_offset, 'custom', DateTimeItemInterface::DATETIME_STORAGE_FORMAT, DateTimeItemInterface::STORAGE_TIMEZONE) . "'", TRUE, $this->calculateOffset), $this->dateFormat, TRUE);
    $b = new DateTimePlus($this->value['max'], new \DateTimeZone($timezone));
    $b = $this->query->getDateFormat($this->query->getDateField("'" . $this->dateFormatter->format($b->getTimestamp() + $origin_offset, 'custom', DateTimeItemInterface::DATETIME_STORAGE_FORMAT, DateTimeItemInterface::STORAGE_TIMEZONE) . "'", TRUE, $this->calculateOffset), $this->dateFormat, TRUE);

    // This is safe because we are manually scrubbing the values.
    $operator = strtoupper($this->operator);

    $end_field = str_replace('__value', '__end_value', $field);
    $formatted_field = $this->query->getDateFormat($this->query->getDateField($field, TRUE, $this->calculateOffset), $this->dateFormat, TRUE);
    $formatted_end_field = $this->query->getDateFormat($this->query->getDateField($end_field, TRUE, $this->calculateOffset), $this->dateFormat, TRUE);

    // 开始日期要么为空，要么已经开始（开始日期 <= $b）
    $where = "($field IS NULL OR $formatted_field <= $b)";
    // 结束日期要么为空，要么还未结束（结束日期 > $a）
    $where .= ' AND ';
    $where .= "($end_field IS NULL OR $formatted_end_field > $a)";

    if ($operator == 'NOT BETWEEN') {
      $where = "NOT ($where)";
    }

    $this->query->addWhereExpression($this->options['group'], $where);
  }

  protected function opSimple($field) {
    $timezone = $this->getTimezone();
    $origin_offset = $this->getOffset($this->value['value'], $timezone);

    // Convert to ISO. UTC timezone is used since dates are stored in UTC.
    $value = new DateTimePlus($this->value['value'], new \DateTimeZone($timezone));
    $value = $this->query->getDateFormat($this->query->getDateField("'" . $this->dateFormatter->format($value->getTimestamp() + $origin_offset, 'custom', DateTimeItemInterface::DATETIME_STORAGE_FORMAT, DateTimeItemInterface::STORAGE_TIMEZONE) . "'", TRUE, $this->calculateOffset), $this->dateFormat, TRUE);

    // This is safe because we are manually scrubbing the value.
    $end_field = str_replace('__value', '__end_value', $field);
    $formatted_field = $this->query->getDateFormat($this->query->getDateField($field, TRUE, $this->calculateOffset), $this->dateFormat, TRUE);
    $formatted_end_field = $this->query->getDateFormat($this->query->getDateField($end_field, TRUE, $this->calculateOffset), $this->dateFormat, TRUE);

    switch ($this->operator) {
      case '<':
        $where = "($field IS NULL OR $formatted_field $this->operator $value)";
        $where .= ' AND ';
        $where .= "($end_field IS NULL OR $end_field = '' OR $formatted_end_field >= $value)";
        break;

      case '<=':
        $where = "($field IS NULL OR $formatted_field $this->operator $value)";
        $where .= ' AND ';
        $where .= "($end_field IS NULL OR $end_field = '' OR $formatted_end_field >= $value)";
        break;

      case '>=':
        $where = "($end_field IS NULL OR $end_field = '' OR $formatted_end_field >= $value OR $formatted_field >= $value)";
        break;

      case '>':
        $where = "($end_field IS NULL OR $end_field = '' OR $formatted_end_field > $value OR $formatted_field > $value )";
        break;

      default:
        // 开始日期要么为空，要么已经开始（开始日期 <= $value）
        $where = "($field IS NULL OR $formatted_field <= $value)";
        // 结束日期要么为空，要么还未结束（结束日期 >= $value）
        $where .= ' AND ';
        $where .= "($end_field IS NULL OR $end_field = '' OR $formatted_end_field >= $value)";

        if ($this->operator == '!=') {
          $where = "NOT ($where)";
        }
        break;
    }
    $this->query->addWhereExpression($this->options['group'], $where);
  }

}
