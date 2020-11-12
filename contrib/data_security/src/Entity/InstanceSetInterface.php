<?php

namespace Drupal\data_security\Entity;

use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntityInterface;

/**
 * Provides an interface for defining Instance set entities.
 *
 * @ingroup data_security
 */
interface InstanceSetInterface extends EffectiveDatesBusinessGroupEntityInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Instance set name.
   *
   * @return string
   *   Name of the Instance set.
   */
  public function getName();

  /**
   * Sets the Instance set name.
   *
   * @param string $name
   *   The Instance set name.
   *
   * @return \Drupal\data_security\Entity\InstanceSetInterface
   *   The called Instance set entity.
   */
  public function setName($name);

  /**
   * Gets the Instance set creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Instance set.
   */
  public function getCreatedTime();

  /**
   * Sets the Instance set creation timestamp.
   *
   * @param int $timestamp
   *   The Instance set creation timestamp.
   *
   * @return \Drupal\data_security\Entity\InstanceSetInterface
   *   The called Instance set entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * @return boolean
   */
  public function withinScope($entity_id);

}
