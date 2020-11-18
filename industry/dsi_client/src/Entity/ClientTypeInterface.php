<?php

namespace Drupal\dsi_client\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Client type entities.
 */
interface ClientTypeInterface extends ConfigEntityInterface {

  // Add get/set methods for your configuration properties here.
  public function getTargetEntityTypeId();
}
