<?php

namespace Drupal\dsi_device\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Devices.
 *
 * @ingroup dsi_device
 */
interface DeviceInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Device name.
   *
   * @return string
   *   Name of the Device.
   */
  public function getName();

  /**
   * Sets the Device name.
   *
   * @param string $name
   *   The Device name.
   *
   * @return \Drupal\dsi_device\Entity\DeviceInterface
   *   The called Device entity.
   */
  public function setName($name);

  /**
   * Gets the Device creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Device.
   */
  public function getCreatedTime();

  /**
   * Sets the Device creation timestamp.
   *
   * @param int $timestamp
   *   The Device creation timestamp.
   *
   * @return \Drupal\dsi_device\Entity\DeviceInterface
   *   The called Device entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the Device revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the Device revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\dsi_device\Entity\DeviceInterface
   *   The called Device entity.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the Device revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the Device revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\dsi_device\Entity\DeviceInterface
   *   The called Device entity.
   */
  public function setRevisionUserId($uid);

}
