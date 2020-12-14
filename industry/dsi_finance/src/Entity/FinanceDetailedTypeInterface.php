<?php

namespace Drupal\dsi_finance\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Finance detailed type entities.
 */
interface FinanceDetailedTypeInterface extends ConfigEntityInterface {

  public function getTargetEntityTypeId();
}
