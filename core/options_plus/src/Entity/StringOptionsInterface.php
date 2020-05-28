<?php

namespace Drupal\options_plus\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining String options entities.
 */
interface StringOptionsInterface extends ConfigEntityInterface {

  /**
   * @return array
   */
  public function getAllowedValues();

}
