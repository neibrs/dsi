<?php

namespace Drupal\responsibility\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining User responsibilities.
 *
 * @ingroup responsibility
 */
interface UserResponsibilityInterface extends ContentEntityInterface, EntityChangedInterface{

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the User responsibility name.
   *
   * @return string
   *   Name of the User responsibility.
   */
  public function getName();

  /**
   * Sets the User responsibility name.
   *
   * @param string $name
   *   The User responsibility name.
   *
   * @return \Drupal\responsibility\Entity\UserResponsibilityInterface
   *   The called User responsibility entity.
   */
  public function setName($name);

  /**
   * Gets the User responsibility creation timestamp.
   *
   * @return int
   *   Creation timestamp of the User responsibility.
   */
  public function getCreatedTime();

  /**
   * Sets the User responsibility creation timestamp.
   *
   * @param int $timestamp
   *   The User responsibility creation timestamp.
   *
   * @return \Drupal\responsibility\Entity\UserResponsibilityInterface
   *   The called User responsibility entity.
   */
  public function setCreatedTime($timestamp);

}
