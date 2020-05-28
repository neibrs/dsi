<?php

namespace Drupal\layout_template\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\user\EntityOwnerInterface;

/**
 * Provides an interface for defining Layout template entities.
 *
 * @ingroup layout_template
 */
interface LayoutTemplateInterface extends ContentEntityInterface, EntityChangedInterface, EntityOwnerInterface {

  // Add get/set methods for your configuration properties here.

  /**
   * Gets the Layout template name.
   *
   * @return string
   *   Name of the Layout template.
   */
  public function getName();

  /**
   * Sets the Layout template name.
   *
   * @param string $name
   *   The Layout template name.
   *
   * @return \Drupal\layout_template\Entity\LayoutTemplateInterface
   *   The called Layout template entity.
   */
  public function setName($name);

  /**
   * Gets the Layout template creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Layout template.
   */
  public function getCreatedTime();

  /**
   * Sets the Layout template creation timestamp.
   *
   * @param int $timestamp
   *   The Layout template creation timestamp.
   *
   * @return \Drupal\layout_template\Entity\LayoutTemplateInterface
   *   The called Layout template entity.
   */
  public function setCreatedTime($timestamp);

  /**
   * @return bool
   */
  public function isPublic();

  /**
   * @return \Drupal\Core\Config\Entity\ConfigEntityInterface
   */
  public function getRelatedConfig();

  /**
   * @return array
   */
  public function getConfiguration();

}
