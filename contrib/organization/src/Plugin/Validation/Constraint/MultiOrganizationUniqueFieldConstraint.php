<?php

namespace Drupal\organization\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * Checks if the entity name is validate.
 *
 * @Constraint(
 *   id = "MultiOrganizationUniqueField",
 *   label = @Translation("Multi organization unique field constraint")
 * )
 */
class MultiOrganizationUniqueFieldConstraint extends Constraint implements ConstraintValidatorInterface {

  public $message = 'A @entity_type with @field_name %value in @organization already exists.';

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
    // Using isset() instead of !empty() as 0 and '0' are valid ID values for
    // entity types using string IDs.
    if (isset($entity_id)) {
      $query->condition($id_key, $entity_id, '<>');
    }

    $query->condition($field_name, $item->value);

    $entity_type = \Drupal::entityTypeManager()->getDefinition($entity_type_id);
    if ($multiple_organization_classification = $entity_type->get('multiple_organization_classification')) {
      if ($organization = $entity->get($multiple_organization_classification)->entity) {
        $query->condition($multiple_organization_classification, $organization->id());
      }
    }
    else if ($multiple_organization_field = $entity_type->get('multiple_organization_field')) {
      $field_storage_definitions = \Drupal::service('entity_field.manager')->getFieldStorageDefinitions($entity_type_id);
      $field_storage_definition = $field_storage_definitions[$multiple_organization_field];
      $target_entity_type_id = $field_storage_definition->getSetting('target_type');
      $target_entity_type = \Drupal::entityTypeManager()->getDefinition($target_entity_type_id);
      $target_entity_id = $entity->get($multiple_organization_field)->target_id;
      if (!$target_entity_id) return;
      $target_entity = \Drupal::entityTypeManager()->getStorage($target_entity_type_id)->load($target_entity_id);
      $multiple_organization_classification = $target_entity_type->get('multiple_organization_classification');
      if ($multiple_organization_classification) {
        if ($organization = $target_entity->get($multiple_organization_classification)->entity) {
          $query->condition($multiple_organization_field . '.entity.' . $multiple_organization_classification, $organization->id());
        }
      }
    }
    else {
      throw new \Exception('Entity ' . $entity_type_id . ' does not defined multiple_organization_classification or multiple_organization field.');
    }

    $value_taken = (bool) $query
      ->range(0, 1)
      ->count()
      ->execute();

    if ($value_taken) {
      $this->context->addViolation($constraint->message, [
        '%value' => $item->value,
        '@entity_type' => $entity->getEntityType()->getLowercaseLabel(),
        '@field_name' => mb_strtolower($items->getFieldDefinition()->getLabel()),
        '@organization' => isset($organization) ? $organization->label() : '',
      ]);
    }
  }
}