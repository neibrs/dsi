<?php

namespace Drupal\report\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Report entities.
 */
interface ReportInterface extends ConfigEntityInterface {

  /**
   * @return string
   */
  public function getPluginId();

  /**
   * @return \Drupal\report\Entity\ReportInterface
   */
  public function setPluginId($plugin_id);

  /**
   * Returns the plugin instance.
   *
   * @return \Drupal\report\Plugin\ReportPluginInterface
   *   The plugin instance for this report.
   */
  public function getPlugin();

  /**
   * @return string
   */
  public function getCategory();

  /**
   * @param $category
   *
   * @return \Drupal\report\Entity\ReportInterface
   */
  public function setCategory($category);

  public function getFiltersOverride();

  public function setFiltersOverride($filters_override);

}
