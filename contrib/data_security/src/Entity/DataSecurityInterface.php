<?php

namespace Drupal\data_security\Entity;

use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntityInterface;

/**
 * Provides an interface for defining Data securities.
 *
 * @ingroup data_security
 */
interface DataSecurityInterface extends EffectiveDatesBusinessGroupEntityInterface {

  /**
   * Gets the Data security name.
   *
   * @return string
   *   Name of the Data security.
   */
  public function getName();

  /**
   * Sets the Data security name.
   *
   * @param string $name
   *   The Data security name.
   *
   * @return \Drupal\data_security\Entity\DataSecurityInterface
   *   The called Data security entity.
   */
  public function setName($name);

  /**
   * @return boolean
   */
  public function withinScope($entity_id);

}
