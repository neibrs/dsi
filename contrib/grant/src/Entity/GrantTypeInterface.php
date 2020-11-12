<?php

namespace Drupal\grant\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityDescriptionInterface;

/**
 * Provides an interface for defining Grant type entities.
 */
interface GrantTypeInterface extends ConfigEntityInterface, EntityDescriptionInterface {

  // Add get/set methods for your configuration properties here.
}
