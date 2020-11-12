<?php

namespace Drupal\dsi_cases\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Cases entities.
 *
 * @ingroup dsi_cases
 */
interface CasesInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Cases name.
   *
   * @return string
   *   Name of the Cases.
   */
  public function getName();

  /**
   * Sets the Cases name.
   *
   * @param string $name
   *   The Cases name.
   *
   * @return \Drupal\dsi_cases\Entity\CasesInterface
   *   The called Cases entity.
   */
  public function setName($name);

  /**
   * Gets the Cases creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Cases.
   */
  public function getCreatedTime();

  /**
   * Sets the Cases creation timestamp.
   *
   * @param int $timestamp
   *   The Cases creation timestamp.
   *
   * @return \Drupal\dsi_cases\Entity\CasesInterface
   *   The called Cases entity.
   */
  public function setCreatedTime($timestamp);

}
