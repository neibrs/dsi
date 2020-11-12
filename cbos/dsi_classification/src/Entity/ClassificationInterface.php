<?php

namespace Drupal\dsi_classification\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Classification entities.
 */
interface ClassificationInterface extends ConfigEntityInterface {

  public function getTargetEntityBundleId();

  public function setTargetEntityBundleId();

  public function getTargetEntityTypeId();

  /**
   * @return \Drupal\dsi_classification\Entity\ClassificationInterface
   */
  public function setTargetEntityTypeId($target_entity_type_id);

  public function getCollections();

  public function setCollections($collections = []);

}
