<?php

namespace Drupal\eabax_workflows;

interface EntityTransitionInterface {

  /**
   * Gets the transition's conditions.
   *
   * @return mixed
   */
  public function getConditions();

  /**
   * Sets the transition's conditions.
   *
   * @param $condition_id
   * @param $configuration
   *
   * @return mixed
   */
  public function setConditions($condition_id, $configuration);

}
