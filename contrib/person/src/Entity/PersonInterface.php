<?php

namespace Drupal\person\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\organization\Entity\EffectiveDatesBusinessGroupEntityInterface;

/**
 * Provides an interface for defining persons.
 *
 * @ingroup person
 */
interface PersonInterface extends EffectiveDatesBusinessGroupEntityInterface {

  const ROLE_IMPLEMENTOR = 'implementor';

  /**
   * Gets the Person name.
   *
   * @return string
   *   Name of the Person.
   */
  public function getName();

  /**
   * Sets the Person name.
   *
   * @param string $name
   *   The Person name.
   *
   * @return \Drupal\person\Entity\PersonInterface
   *   The called Person entity.
   */
  public function setName($name);

  /**
   * Returns the organization.
   *
   * @return \Drupal\organization\Entity\OrganizationInterface
   */
  public function getOrganization();

  /**
   * @return \Drupal\organization\Entity\OrganizationInterface
   */
  public function getOrganizationByClassification($classification);

  /**
   * @return \Drupal\organization\Entity\OrganizationInterface[]
   */
  public function getOperatingUnits();

  /**
   * @return \Drupal\person\Entity\PersonTypeInterface
   */
  public function getType();

  public function getUserId();
  
  /**
   * @return string
   */
  public function getPrimaryPhone();
  
  /**
   * @return string
   */
  public function getPrimaryEmail();
  
}
