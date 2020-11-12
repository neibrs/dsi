<?php

namespace Drupal\organization\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Checks if the organization has operating unit classification.
 *
 * @Constraint(
 *   id = "OperatingUnit",
 *   label = @Translation("Operating unit validate")
 * )
 */
class OperatingUnitConstraint extends Constraint implements ConstraintValidatorInterface {

  /**
   * @var \Symfony\Component\Validator\Context\ExecutionContextInterface
   */
  protected $context;

  /**
   * {@inheritdoc}
   */
  public function initialize(ExecutionContextInterface $context) {
    $this->context = $context;
  }

  /**
   * {@inheritdoc}
   */
  public function validatedBy() {
    return get_class($this);
  }

  /**
   * {@inheritdoc}
   */
  public function validate($items, Constraint $constraint) {
    if (!$item = $items->first()) {
      return;
    }

    $organization_id = $items->value;
    $organization = \Drupal::entityTypeManager()->getStorage('organization')
      ->load($organization_id);
    $found = FALSE;
    foreach ($organization->get('classifications') as $value) {
      if ($value->entity->id == 'operating_unit') {
        $found = TRUE;
        break;
      }
    }
    if (!$found) {
      $this->context->addViolation('Organization must be operating unit.');
    }
  }

}
