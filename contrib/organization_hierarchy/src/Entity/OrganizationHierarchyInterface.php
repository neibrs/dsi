<?php

namespace Drupal\organization_hierarchy\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\entity_plus\Entity\EffectiveDatesEntityInterface;

/**
 * Provides an interface for defining Organization hierarchy entities.
 *
 * @ingroup organization_hierarchy
 */
interface OrganizationHierarchyInterface extends EffectiveDatesEntityInterface {

  /**
   * Gets the Position hierarchy name.
   *
   * @return string
   *   Name of the Position hierarchy.
   */
  public function getName();

  /**
   * Sets the Position hierarchy name.
   *
   * @param string $name
   *   The Position hierarchy name.
   *
   * @return \Drupal\position\Entity\PositionHierarchyInterface
   *   The called Position hierarchy entity.
   */
  public function setName($name);

  /**
   * @return \Drupal\organization\Entity\OrganizationInterface
   */
  public function getOrganization();

}
