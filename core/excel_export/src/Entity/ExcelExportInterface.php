<?php

namespace Drupal\excel_export\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Excel export entities.
 */
interface ExcelExportInterface extends ConfigEntityInterface {

  /**
   * @return array
   */
  public function getParameters();

  public function setParameters($parameters);

  public function getSheets();

  public function setSheets($sheets);

}
