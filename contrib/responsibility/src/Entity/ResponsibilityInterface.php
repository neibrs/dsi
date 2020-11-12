<?php

namespace Drupal\responsibility\Entity;

use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntityInterface;

/**
 * Provides an interface for defining Responsibility entities.
 *
 * @ingroup responsibility
 */
interface ResponsibilityInterface extends EffectiveDatesBusinessGroupEntityInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Responsibility name.
   *
   * @return string
   *   Name of the Responsibility.
   */
  public function getName();

  /**
   * Sets the Responsibility name.
   *
   * @param string $name
   *   The Responsibility name.
   *
   * @return \Drupal\responsibility\Entity\ResponsibilityInterface
   *   The called Responsibility entity.
   */
  public function setName($name);

}
