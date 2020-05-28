<?php

namespace Drupal\eabax_workflows;

interface EntityTransitionInterface {

  /**
   * Gets the transition's conditions.
   *
   * @return mixed
   */
  public function getConditions();

}
