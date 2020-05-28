<?php

namespace Drupal\entity_plus\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;
use Drupal\Core\Entity\EntityPublishedInterface;

interface EffectiveDatesEntityInterface extends ContentEntityInterface, EntityChangedInterface, EntityPublishedInterface  {

  /**
   * Gets the Position hierarchy creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Position hierarchy.
   */
  public function getCreatedTime();

  /**
   * Sets the Position hierarchy creation timestamp.
   *
   * @param int $timestamp
   *   The Position hierarchy creation timestamp.
   *
   * @return \Drupal\position\Entity\PositionHierarchyInterface
   *   The called Position hierarchy entity.
   */
  public function setCreatedTime($timestamp);

}
