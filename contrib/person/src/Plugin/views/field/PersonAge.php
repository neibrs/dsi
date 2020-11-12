<?php

namespace Drupal\person\Plugin\views\field;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\views\Plugin\views\field\NumericField;
use Drupal\views\ResultRow;

/**
 * @ViewsField("person_age")
 */
class PersonAge extends NumericField {

  /**
   * {@inheritdoc}
   */
  public function getValue(ResultRow $values, $field = NULL) {
    $date = parent::getValue($values, $field);
    if (empty($date)) return 0;

    $date = DrupalDateTime::createFromFormat('Y-m-d', $date);

    return $date->diff(new \DateTime())->y;
  }

}
