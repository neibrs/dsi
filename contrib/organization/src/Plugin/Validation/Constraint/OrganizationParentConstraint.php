<?php

namespace Drupal\organization\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Checks if the organization's parent is validate.
 *
 * @Constraint(
 *   id = "OrganizationParent",
 *   label = @Translation("Organization parent validate")
 * )
 */
class OrganizationParentConstraint extends Constraint implements ConstraintValidatorInterface {

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

    $value = $item->getValue()['target_id'];

    $entity = $items->getEntity();
    if ($id = $entity->id()) {
      if ($value == $id) {
        $this->context->addViolation('Parent organization could not be refer to itself.');
      }
      else {
        $children = $entity->loadAllChildren();
        if (in_array($value, array_keys($children))) {
          $this->context->addViolation("Parent organization could not be refer to it's children.");
        }
      }
    }
  }

}
