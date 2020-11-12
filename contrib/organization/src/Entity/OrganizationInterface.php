<?php

namespace Drupal\organization\Entity;

/**
 * Provides an interface for defining organizations.
 *
 * @ingroup organization
 */
interface OrganizationInterface extends EffectiveDatesBusinessGroupEntityInterface {

  public function addClassification($classification_id);

  public function getClassifications();

  /**
   * Gets the Organization name.
   *
   * @return string
   *   Name of the Organization.
   */
  public function getName();

  /**
   * Sets the Organization name.
   *
   * @param string $name
   *   The Organization name.
   *
   * @return \Drupal\organization\Entity\OrganizationInterface
   *   The called Organization entity.
   */
  public function setName($name);

  /**
   * Returns the parent organization entity.
   *
   * @return \Drupal\organization\Entity\OrganizationInterface
   *   The parent organization entity.
   */
  public function getParent();

  /**
   * @param $organizations
   * @return mixed
   */
  public function setParent($organizations);

  /**
   * @param $classification
   * @return bool
   */
  public function hasClassification($classification);

  /**
   * @param $classification
   * @return \Drupal\organization\Entity\OrganizationInterface
   */
  public function getByClassification($classification);

  /**
   * @return \Drupal\organization\Entity\OrganizationInterface[]
   */
  public function loadAllChildren();

  /**
   * @param bool $status
   * @return array
   */
  public function loadChildren($status = FALSE);
}
