<?php

namespace Drupal\lookup\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

/**
 * Provides an interface for defining Lookup entities.
 *
 * @ingroup lookup
 */
interface LookupInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Lookup name.
   *
   * @return string
   *   Name of the Lookup.
   */
  public function getName();

  /**
   * Sets the Lookup name.
   *
   * @param string $name
   *   The Lookup name.
   *
   * @return \Drupal\lookup\Entity\LookupInterface
   *   The called Lookup entity.
   */
  public function setName($name);

  /**
   * Gets the Lookup creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Lookup.
   */
  public function getCreatedTime();

  /**
   * Sets the Lookup creation timestamp.
   *
   * @param int $timestamp
   *   The Lookup creation timestamp.
   *
   * @return \Drupal\lookup\Entity\LookupInterface
   *   The called Lookup entity.
   */
  public function setCreatedTime($timestamp);

}
