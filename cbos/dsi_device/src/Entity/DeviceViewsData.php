<?php

namespace Drupal\dsi_device\Entity;

use Drupal\views\EntityViewsData;

/**
 * Provides Views data for Devices.
 */
class DeviceViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    // Additional information for Views integration, such as table joins, can be
    // put here.
    return $data;
  }

}
