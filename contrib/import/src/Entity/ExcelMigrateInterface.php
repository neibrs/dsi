<?php

namespace Drupal\import\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Excel migrate entities.
 */
interface ExcelMigrateInterface extends ConfigEntityInterface {

  /**
   * @return array
   */
  public function getSource();

  public function setSource($source);

  public function getConstants();

  public function setConstant($key, $value);

  /**
   * @return array
   */
  public function getSheets();

  public function setSheets($sheets);

}
