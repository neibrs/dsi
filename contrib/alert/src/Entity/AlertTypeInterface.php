<?php

namespace Drupal\alert\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Alert type entities.
 */
interface AlertTypeInterface extends ConfigEntityInterface {

  /**
   * Gets the description.
   *
   * @return string
   *   The description of this type.
   */
  public function getDescription();

  /**
   * Gets the color.
   *
   * @return string
   *   The color of this type.
   */
  public function getColor();

}
