<?php

namespace Drupal\security_profile\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntityInterface;

/**
 * Provides an interface for defining Security profile entities.
 *
 * @ingroup security_profile
 */
interface SecurityProfileInterface extends EffectiveDatesBusinessGroupEntityInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Security profile name.
   *
   * @return string
   *   Name of the Security profile.
   */
  public function getName();

  /**
   * Sets the Security profile name.
   *
   * @param string $name
   *   The Security profile name.
   *
   * @return \Drupal\security_profile\Entity\SecurityProfileInterface
   *   The called Security profile entity.
   */
  public function setName($name);

}
