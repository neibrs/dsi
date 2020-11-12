<?php

namespace Drupal\eabax_workflows;

use Drupal\workflows\Transition;

class EntityTransition extends Transition implements EntityTransitionInterface {

  /**
   * The conditions for transition.
   * @var array
   */
  protected $conditions;

  /**
   * {@inheritdoc}
   */
  public function __construct($workflow, $id, $label, array $from_state_ids, $to_state_id, $weight, $conditions = []) {
    parent::__construct($workflow, $id, $label, $from_state_ids, $to_state_id, $weight);

    $this->conditions = $conditions;
  }

  /**
   * {@inheritdoc}
   */
  public function getConditions() {
    return $this->conditions;
  }

  /**
   * {@inheritdoc}
   */
  public function setConditions($condition_id, $condition) {
    $this->conditions[$condition_id] = $condition;
  }
}
