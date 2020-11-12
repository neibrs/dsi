<?php

namespace Drupal\dsi_record\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Record entities.
 *
 * @ingroup dsi_record
 */
interface RecordInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Record name.
   *
   * @return string
   *   Name of the Record.
   */
  public function getName();

  /**
   * Sets the Record name.
   *
   * @param string $name
   *   The Record name.
   *
   * @return \Drupal\dsi_record\Entity\RecordInterface
   *   The called Record entity.
   */
  public function setName($name);

  /**
   * Gets the Record creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Record.
   */
  public function getCreatedTime();

  /**
   * Sets the Record creation timestamp.
   *
   * @param int $timestamp
   *   The Record creation timestamp.
   *
   * @return \Drupal\dsi_record\Entity\RecordInterface
   *   The called Record entity.
   */
  public function setCreatedTime($timestamp);

}
