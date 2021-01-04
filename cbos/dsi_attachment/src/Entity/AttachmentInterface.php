<?php

namespace Drupal\dsi_attachment\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Attachment entities.
 *
 * @ingroup dsi_attachment
 */
interface AttachmentInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Attachment creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Attachment.
   */
  public function getCreatedTime();

  /**
   * Sets the Attachment creation timestamp.
   *
   * @param int $timestamp
   *   The Attachment creation timestamp.
   *
   * @return \Drupal\dsi_attachment\Entity\AttachmentInterface
   *   The called Attachment entity.
   */
  public function setCreatedTime($timestamp);

}
