<?php

namespace Drupal\person\Plugin\WorkflowType;

use Drupal\eabax_workflows\Plugin\WorkflowType\EntityWorkflowBase;

/**
 * Attaches workflows to person entity types and their bundles.
 *
 * @WorkflowType(
 *   id = "employee_status",
 *   label = @Translation("Employee status"),
 *   required_states = {
 *     "inactive",
 *     "active",
 *     "leave_of_absence",
 *     "leave_with_pay",
 *     "retired_with_pay",
 *     "terminated_with_pay",
 *     "suspended",
 *     "retired",
 *     "terminated",
 *     "deceased",
 *   },
 * )
 */
class EmployeeStatus extends EntityWorkflowBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'states' => [
        'active' => [
          'label' => 'Active',
          'weight' => 0,
        ],
        'leave_of_absence' => [
          'label' => 'Leave of absence',
          'weight' => 10,
        ],
        'leave_with_pay' => [
          'label' => 'Leave with pay',
          'weight' => 20,
        ],
        'retired_with_pay' => [
          'label' => 'Retired with pay',
          'weight' => 30,
        ],
        'terminated_with_pay' => [
          'label' => 'Terminated with pay',
          'weight' => 40,
        ],
        'suspended' => [
          'label' => 'Suspended',
          'weight' => 50,
        ],
        'retired' => [
          'label' => 'Retired',
          'weight' => 60,
        ],
        'terminated' => [
          'label' => 'Terminated',
          'weight' => 70,
        ],
        'deceased' => [
          'label' => 'Deceased',
          'weight' => 80,
        ],
      ],
      'transitions' => [],
      'entity_type_id' => 'person',
    ];
  }

}