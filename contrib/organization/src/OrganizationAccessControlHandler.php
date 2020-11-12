<?php

namespace Drupal\organization;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Organization entity.
 *
 * @see \Drupal\organization\Entity\Organization.
 */
class OrganizationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * @var \Drupal\grant\GrantManagerInterface
   */
  protected $grantManager;

  /**
   * Gets the grant manager.
   *
   * @return \Drupal\grant\GrantManagerInterface
   *   The grant manager.
   */
  public function grantManager() {
    if (!$this->grantManager) {
      $this->grantManager = \Drupal::service('grant.manager');
    }
    return $this->grantManager;
  }

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\organization\Entity\OrganizationInterface $entity */
    switch ($operation) {
      case 'view':
        return AccessResult::allowedIf($this->grantManager()->hasEntityPermission($entity, 'view organizations', $account));

      case 'update':
        return AccessResult::allowedIf($this->grantManager()->hasEntityPermission($entity, 'maintain organizations', $account));

      case 'delete':
        return AccessResult::neutral();
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'maintain organizations');
  }

  /**
   * {@inheritdoc}
   */
  protected function checkFieldAccess($operation, FieldDefinitionInterface $field_definition, AccountInterface $account, FieldItemListInterface $items = NULL) {
    // 组织是业务组才能访问 currency
    if ($items && $field_definition->getName() == 'currency') {
      /** @var \Drupal\organization\Entity\OrganizationInterface $entity */
      $entity = $items->getEntity();
      if (!$entity->hasClassification('business_group')) {
        return AccessResult::forbidden();
      }
    }

    // 是否具备管理权限才能设置 master 字段？

    return parent::checkFieldAccess($operation, $field_definition, $account, $items);
  }

}
