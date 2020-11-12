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
  public function setPublished() {
    $key = $this->getEntityType()->getKey('published');
    $this->set($key, TRUE);

    // 这里只设置状态，不要设置 effective_dates 的时间，否则会篡改用户录入的时间。
    // 原则：根据时间设置状态，不根据状态设置时间.

    return $this;
  }

  public function setUnpublished() {
    $key = $this->getEntityType()->getKey('published');
    $this->set($key, FALSE);

    // 这里只设置状态，不要设置 effective_dates 的时间，否则会篡改用户录入的时间。
    // 原则：根据时间设置状态，不根据状态设置时间.

    return $this;
  }

  public function setPublishedByEffectiveDates() {
    $today = date('Y-m-d', \Drupal::request()->server->get('REQUEST_TIME'));
    // 如果未设置effective_dates，不要修改状态.
    if ($start_date = $this->get('effective_dates')->value) {
      if ($today < $start_date) {
        $this->setUnpublished();
      }
    }
    if ($end_date = $this->get('effective_dates')->end_value) {
      if ($today > $end_date) {
        $this->setUnpublished();
      }
    }
    if ($start_date && $today >= $start_date && (empty($end_date) || $today < $end_date)) {
      $this->setPublished();
    }
  }
}
