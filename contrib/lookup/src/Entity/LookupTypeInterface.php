<?php

namespace Drupal\lookup\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;
use Drupal\Core\Entity\EntityDescriptionInterface;

/**
 * Provides an interface for defining Lookup type entities.
 */
interface LookupTypeInterface extends ConfigEntityInterface, EntityDescriptionInterface {}
