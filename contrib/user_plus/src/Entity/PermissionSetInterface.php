<?php

namespace Drupal\user_plus\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntityInterface;

/**
 * Provides an interface for defining Permission sets.
 *
 * @ingroup user_plus
 */
interface PermissionSetInterface extends EffectiveDatesBusinessGroupEntityInterface {

  /**
   * Gets the Permission set name.
   *
   * @return string
   *   Name of the Permission set.
   */
  public function getName();

  /**
   * Sets the Permission set name.
   *
   * @param string $name
   *   The Permission set name.
   *
   * @return \Drupal\user_plus\Entity\PermissionSetInterface
   *   The called Permission set entity.
   */
  public function setName($name);

  /**
   * @return string[]
   */
  public function getPermissions();

  /**
   * @return string[]
   */
  public function getAllPermissions();

  public function setPermissions($permissions);

  /**
   * @return boolean
   */
  public function hasPermission($permission);

}
