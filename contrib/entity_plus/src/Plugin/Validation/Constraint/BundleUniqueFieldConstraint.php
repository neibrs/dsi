<?php

namespace Drupal\entity_plus\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Checks if the entity name is validate.
 *
 * @Constraint(
 *   id = "BundleUniqueField",
 *   label = @Translation("Bundle unique field constraint")
 * )
 */
class BundleUniqueFieldConstraint extends Constraint implements ConstraintValidatorInterface {

  public $message = 'A @entity_type with @field_name %value in @bundle already exists.';

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
    $field_name = $items->getFieldDefinition()->getName();
    /** @var \Drupal\Core\Entity\EntityInterface $entity */
    $entity = $items->getEntity();
    $entity_type_id = $entity->getEntityTypeId();
    $id_key = $entity->getEntityType()->getKey('id');

    $query = \Drupal::entityQuery($entity_type_id);

    $entity_id = $entity->id();
    if (isset($entity_id)) {
      $query->condition($id_key, $entity_id, '<>');
    }
    $query->condition($field_name, $item->value);

    $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);
    if ($bundle = $entity->bundle()) {
      $query->condition($entity_type->getKey('bundle'), $bundle);
    }

    $value_taken = (bool) $query
      ->range(0, 1)
      ->count()
      ->execute();

    if ($value_taken) {
      $this->context->addViolation($constraint->message, [
        '%value' => $item->value,
        '@entity_type' => $entity->getEntityType()->getLowercaseLabel(),
        '@bundle' => !empty($bundle) ? $bundle : '',
        ]);
    }

  }

}
