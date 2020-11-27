<?php

namespace Drupal\alert\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining alerts.
 *
 * @ingroup alert
 */
interface AlertInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Alert name.
   *
   * @return string
   *   Name of the Alert.
   */
  public function getName();

  /**
   * Sets the Alert name.
   *
   * @param string $name
   *   The Alert name.
   *
   * @return \Drupal\alert\Entity\AlertInterface
   *   The called Alert entity.
   */
  public function setName($name);

  /**
   * Gets the Alert creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Alert.
   */
  public function getCreatedTime();

  /**
   * Sets the Alert creation timestamp.
   *
   * @param int $timestamp
   *   The Alert creation timestamp.
   *
   * @return \Drupal\alert\Entity\AlertInterface
   *   The called Alert entity.
   */
  public function setCreatedTime($timestamp);

}
