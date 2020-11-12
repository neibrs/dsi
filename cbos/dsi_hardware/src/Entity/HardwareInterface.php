<?php

namespace Drupal\dsi_hardware\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Hardware entities.
 *
 * @ingroup dsi_hardware
 */
interface HardwareInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Hardware name.
   *
   * @return string
   *   Name of the Hardware.
   */
  public function getName();

  /**
   * Sets the Hardware name.
   *
   * @param string $name
   *   The Hardware name.
   *
   * @return \Drupal\dsi_hardware\Entity\HardwareInterface
   *   The called Hardware entity.
   */
  public function setName($name);

  /**
   * Gets the Hardware creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Hardware.
   */
  public function getCreatedTime();

  /**
   * Sets the Hardware creation timestamp.
   *
   * @param int $timestamp
   *   The Hardware creation timestamp.
   *
   * @return \Drupal\dsi_hardware\Entity\HardwareInterface
   *   The called Hardware entity.
   */
  public function setCreatedTime($timestamp);

}
