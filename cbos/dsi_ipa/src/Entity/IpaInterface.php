<?php

namespace Drupal\dsi_ipa\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\RevisionLogInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining IP Addresses.
 *
 * @ingroup dsi_ipa
 */
interface IpaInterface extends ContentEntityInterface, RevisionLogInterface, EntityChangedInterface, EntityPublishedInterface, EntityOwnerInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the IP Address name.
   *
   * @return string
   *   Name of the IP Address.
   */
  public function getName();

  /**
   * Sets the IP Address name.
   *
   * @param string $name
   *   The IP Address name.
   *
   * @return \Drupal\dsi_ipa\Entity\IpaInterface
   *   The called IP Address.
   */
  public function setName($name);

  /**
   * Gets the IP Address creation timestamp.
   *
   * @return int
   *   Creation timestamp of the IP Address.
   */
  public function getCreatedTime();

  /**
   * Sets the IP Address creation timestamp.
   *
   * @param int $timestamp
   *   The IP Address creation timestamp.
   *
   * @return \Drupal\dsi_ipa\Entity\IpaInterface
   *   The called IP Address.
   */
  public function setCreatedTime($timestamp);

  /**
   * Gets the IP Address revision creation timestamp.
   *
   * @return int
   *   The UNIX timestamp of when this revision was created.
   */
  public function getRevisionCreationTime();

  /**
   * Sets the IP Address revision creation timestamp.
   *
   * @param int $timestamp
   *   The UNIX timestamp of when this revision was created.
   *
   * @return \Drupal\dsi_ipa\Entity\IpaInterface
   *   The called IP Address.
   */
  public function setRevisionCreationTime($timestamp);

  /**
   * Gets the IP Address revision author.
   *
   * @return \Drupal\user\UserInterface
   *   The user entity for the revision author.
   */
  public function getRevisionUser();

  /**
   * Sets the IP Address revision author.
   *
   * @param int $uid
   *   The user ID of the revision author.
   *
   * @return \Drupal\dsi_ipa\Entity\IpaInterface
   *   The called IP Address.
   */
  public function setRevisionUserId($uid);

}
