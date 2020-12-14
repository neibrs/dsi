<?php

namespace Drupal\dsi_finance\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Finance type entities.
 */
interface FinanceTypeInterface extends ConfigEntityInterface {

  public function getTargetEntityTypeId();
}
