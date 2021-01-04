<?php

namespace Drupal\dsi_attachment\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Attachment directory entities.
 *
 * @ingroup dsi_attachment
 */
interface AttachmentDirectoryInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Attachment directory name.
   *
   * @return string
   *   Name of the Attachment directory.
   */
  public function getName();

  /**
   * Sets the Attachment directory name.
   *
   * @param string $name
   *   The Attachment directory name.
   *
   * @return \Drupal\dsi_attachment\Entity\AttachmentDirectoryInterface
   *   The called Attachment directory entity.
   */
  public function setName($name);

  /**
   * Gets the Attachment directory creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Attachment directory.
   */
  public function getCreatedTime();

  /**
   * Sets the Attachment directory creation timestamp.
   *
   * @param int $timestamp
   *   The Attachment directory creation timestamp.
   *
   * @return \Drupal\dsi_attachment\Entity\AttachmentDirectoryInterface
   *   The called Attachment directory entity.
   */
  public function setCreatedTime($timestamp);

}
