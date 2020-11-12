<?php

namespace Drupal\grant\Entity;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntityInterface;

/**
 * Provides an interface for defining Grants.
 *
 * @ingroup grant
 */
interface GrantInterface extends EffectiveDatesBusinessGroupEntityInterface {

  /**
   * Add get/set methods for your configuration properties here.
   */

  /**
   * Gets the Grant name.
   *
   * @return string
   *   Name of the Grant.
   */
  public function getName();

  /**
   * Sets the Grant name.
   *
   * @param string $name
   *   The Grant name.
   *
   * @return \Drupal\grant\Entity\GrantInterface
   *   The called Grant entity.
   */
  public function setName($name);

  /**
   * @return \Drupal\user_plus\Entity\PermissionSetInterface
   */
  public function getPermissionSet();

  /**
   * @return \Drupal\data_security\Entity\DataSecurityInterface
   */
  public function getDataSecurity();

  /**
   * @return boolean
   */
  public function hasEntityPermission(EntityInterface $entity, $permission, AccountInterface $account = NULL);

}
