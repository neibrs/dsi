<?php

namespace Drupal\entity_filter\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Predefined filter entities.
 */
interface PredefinedFilterInterface extends ConfigEntityInterface {

  public function getFilters();

  /**
   * @return \Drupal\entity_filter\Entity\PredefinedFilterInterface
   */
  public function setFilters($filters);

  public function getRelationships();

  public function getRelationship($name);

  /**
   * @return \Drupal\entity_filter\Entity\PredefinedFilterInterface
   */
  public function setRelationship($name, $relationship);

}
