<?php

namespace Drupal\dsi_litigant\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Litigant entities.
 *
 * @ingroup dsi_litigant
 */
interface LitigantInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Litigant creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Litigant.
   */
  public function getCreatedTime();

  /**
   * Sets the Litigant creation timestamp.
   *
   * @param int $timestamp
   *   The Litigant creation timestamp.
   *
   * @return \Drupal\dsi_litigant\Entity\LitigantInterface
   *   The called Litigant entity.
   */
  public function setCreatedTime($timestamp);

}
