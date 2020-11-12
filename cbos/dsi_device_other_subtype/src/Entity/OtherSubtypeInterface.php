<?php

namespace Drupal\dsi_device_other_subtype\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Other subtype entities.
 */
interface OtherSubtypeInterface extends ConfigEntityInterface {

  public function getLocations();

  public function setLocations($locations = []);

}
