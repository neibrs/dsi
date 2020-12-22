<?php

namespace Drupal\dsi_finance\Entity;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Provides an interface for defining Finance expenditure type entities.
 */
interface FinanceExpenditureTypeInterface extends ConfigEntityInterface {

  public function getTargetEntityTypeId();
}
