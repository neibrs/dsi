<?php

namespace Drupal\entity_plus;

use Drupal\Core\Entity\Query\QueryInterface;

interface EntityPlusManagerInterface {

  public function calculateFormula($formula = NULL, array $contexts);

  /**
   * 为 query 添加有效期条件.
   */
  public function addEffectiveDatesCondition(QueryInterface $query, $start_date = NULL, $end_date = NULL, $prefix = NULL);

}
