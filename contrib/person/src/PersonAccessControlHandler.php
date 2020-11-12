<?php

namespace Drupal\person;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;
use Drupal\grant\Entity\EntityAccessControlHandler;

/**
 * Access controller for the Person entity.
 *
 * @see \Drupal\person\Entity\Person.
 */
class PersonAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\person\Entity\PersonInterface $entity */
    switch ($operation) {
      case 'view':
        if ($this->grantManager()->hasEntityPermission($entity, 'view persons', $account)) {
          return AccessResult::allowed();
        }
        if ($this->grantManager()->hasEntityPermission($entity, 'view ' . $entity->bundle() . ' persons', $account)) {
          return AccessResult::allowed();
        }
        if ($account->hasPermission('view own profile')) {
          if ($person = \Drupal::service('person.manager')->getPersonByUser($account->id())) {
            AccessResult::allowedIf($person->id() == $entity->id());
          }
        }
        break;

      case 'update':
        if ($account->hasPermission('maintain persons') || $account->hasPermission('maintain ' . $entity->bundle() . ' persons')) {
          return AccessResult::allowed()->cachePerPermissions();
        }
        if ($account->hasPermission('edit own profile')) {
          $user = \Drupal::entityTypeManager()->getStorage('user')->load($account->id());
          if ($user->get('person')->target_id == $entity->id()) {
            return AccessResult::allowed();
          }
        }

        break;

      case 'delete':
        return AccessResult::neutral();

      case 'terminate':
        if ($entity->bundle() != 'employee') {
          return AccessResult::forbidden('Only employee can be terminate.');
        }
        return AccessResult::allowedIfHasPermissions($account, [
          'maintain persons',
          'maintain employee persons',
        ], 'OR');
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    /** @var \Drupal\person\Entity\PersonTypeInterface $person_type */
    if ($entity_bundle && $person_type = \Drupal::entityTypeManager()->getStorage('person_type')->load($entity_bundle)) {
      if (!$person_type->status()) {
        return AccessResult::forbidden();
      }
    }

    return AccessResult::allowedIfHasPermission($account, 'maintain persons');
  }

  /**
   * {@inheritdoc}
   */
  protected function checkFieldAccess($operation, FieldDefinitionInterface $field_definition, AccountInterface $account, FieldItemListInterface $items = NULL) {
    // Do not allow edit some fields if only have the 'edit own profile' permission.
    $administrative_fields = ['effective_dates', 'status', 'number', 'type', 'organization', 'field_employee_number', 'hire_date', 'rehire_date', 'adjusted_service_date'];
    if ($operation == 'edit' && in_array($field_definition->getName(), $administrative_fields)) {
      return AccessResult::allowedIfHasPermission($account, 'maintain persons');
    }

    switch ($field_definition->getName()) {
      case 'rehire_date':
        return AccessResult::allowedIf($items->getEntity()->bundle() == 'ex_employee');
    }

    return parent::checkFieldAccess($operation, $field_definition, $account, $items);
  }

}
