<?php

namespace Drupal\person\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Person type entities.
 */
interface PersonTypeInterface extends ConfigEntityInterface {

  /**
   * Determines whether the person type is locked.
   *
   * @return string|false
   *   The module name that locks the type or FALSE.
   */
  public function isLocked();

  public function getSystemType();

  public function getIsSystem();

  /**
   * @return boolean
   */
  public function isEmployee();

  public function loadChildren();

  public function loadAllchildren();

  public function getAlias();
  public function setAlias($alias);
}
