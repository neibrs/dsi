<?php

namespace Drupal\eabax_core\Entity;

use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityPublishedTrait;
use Drupal\Core\Render\Element\Date;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;

/**
 * Provides a trait for accessing effective status.
 */
trait EffectiveDatesTrait {

  use EntityPublishedTrait;

  /**
   * {@inheritdoc}
   */
  public function setPublished($published = NULL) {
    if ($published !== NULL) {
      @trigger_error('The $published parameter is deprecated since version 8.3.x and will be removed in 9.0.0.', E_USER_DEPRECATED);
      $value = (bool) $published;
    }
    else {
      $value = TRUE;
    }
    $key = $this->getEntityType()->getKey('published');
    $this->set($key, $value);

    $today = new DrupalDateTime('now', DateTimeItemInterface::STORAGE_TIMEZONE);
    $this->effective_dates->value = $today->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);

    if ($this->effective_dates->end_value <= $today->format(DateTimeItemInterface::DATE_STORAGE_FORMAT)) {
      $this->effective_dates->end_value = '';
    }

    return $this;
  }

  public function setUnpublished() {
    $key = $this->getEntityType()->getKey('published');
    $this->set($key, FALSE);

    $today = new DrupalDateTime('now', DateTimeItemInterface::STORAGE_TIMEZONE);
    $this->effective_dates->end_value = $today->format(DateTimeItemInterface::DATE_STORAGE_FORMAT);

    return $this;
  }

  public function setPublishedByEffectiveDates() {
    $current_timestamp = date('Y-m-d', \Drupal::request()->server->get('REQUEST_TIME'));
    $effective_start_date = $this->get('effective_dates')->value ?: $current_timestamp;
    $effective_end_date = $this->get('effective_dates')->end_value ?: date('Y-m-d', strtotime("+ 1 day", strtotime($current_timestamp)));

    if ($effective_start_date > $current_timestamp || $effective_end_date <= $current_timestamp) {
      $this->setUnpublished();
    }
    else {
      $this->setPublished();
    }
  }
}
