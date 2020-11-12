<?php

namespace Drupal\administrative_area\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

/**
 * Provides an interface for defining Administrative areas.
 *
 * @ingroup administrative_area
 */
interface AdministrativeAreaInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Administrative area name.
   *
   * @return string
   *   Name of the Administrative area.
   */
  public function getName();

  /**
   * Sets the Administrative area name.
   *
   * @param string $name
   *   The Administrative area name.
   *
   * @return \Drupal\administrative_area\Entity\AdministrativeAreaInterface
   *   The called Administrative area entity.
   */
  public function setName($name);

  /**
   * Gets the Administrative area creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Administrative area.
   */
  public function getCreatedTime();

  /**
   * Sets the Administrative area creation timestamp.
   *
   * @param int $timestamp
   *   The Administrative area creation timestamp.
   *
   * @return \Drupal\administrative_area\Entity\AdministrativeAreaInterface
   *   The called Administrative area entity.
   */
  public function setCreatedTime($timestamp);

}
