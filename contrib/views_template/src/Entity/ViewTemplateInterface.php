<?php

namespace Drupal\views_template\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining View template entities.
 */
interface ViewTemplateInterface extends ConfigEntityInterface {
  
  public function setViewId($view);
  
  public function getViewId();
  
  /**
   * Gets filters.
   *
   * @return array
   */
  public function getFilters();
  
  /**
   * Sets filters.
   *
   * @param array $filters
   *   The filters of view template.
   */
  public function setFilters($filters);
  
  /**
   * Gets fields.
   *
   * @return array
   */
  public function getFields();
  
  /**
   * Sets fields.
   *
   * @param array $fields
   *   The fields of view template.
   */
  public function setFields($fields);
  
  public function getUserId();
  
  public function setUserId($user);
  
  /**
   * @return boolean
   */
  public function getIsPublic();
}
