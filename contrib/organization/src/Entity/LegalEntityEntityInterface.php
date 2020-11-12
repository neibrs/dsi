<?php

namespace Drupal\organization\Entity;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityChangedInterface;

interface LegalEntityEntityInterface extends ContentEntityInterface, EntityChangedInterface {

  /**
   * Gets the Organization creation timestamp.
   *
   * @return int
   *   Creation timestamp of the Organization.
   */
  public function getCreatedTime();

  /**
   * Sets the Organization creation timestamp.
   *
   * @param int $timestamp
   *   The Organization creation timestamp.
   *
   * @return \Drupal\organization\Entity\OrganizationInterface
   *   The called Organization entity.
   */
  public function setCreatedTime($timestamp);

}
