<?php

namespace Drupal\dsi_purchased\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Purchased entities.
 *
 * @ingroup dsi_purchased
 */
interface PurchasedInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Purchased creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Purchased.
   */
  public function getCreatedTime();

  /**
   * Sets the Purchased creation timestamp.
   *
   * @param int $timestamp
   *   The Purchased creation timestamp.
   *
   * @return \Drupal\dsi_purchased\Entity\PurchasedInterface
   *   The called Purchased entity.
   */
  public function setCreatedTime($timestamp);

}
