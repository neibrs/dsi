<?php

namespace Drupal\organization;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

class MultipleOrganizationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    if (!$account->hasPermission('bypass multiple organization access')) {
      if (\Drupal::moduleHandler()->moduleExists('person')) {
        if ($multiple_organization_classification = $entity->getEntityType()->get('multiple_organization_classification')) {
          /** @var \Drupal\person\PersonManagerInterface $person_manager */
          $person_manager = \Drupal::service('person_manager');

          $person = $person_manager->getPersonByUser($account->id());

          // 获得 $account 可访问的多组织
          $multiple_organizations = \Drupal::service('person_manager')
            ->currentPersonAccessibleOrganizationByClassification($multiple_organization_classification);

          if (!in_array($entity->get($multiple_organization_classification)->target_id, array_keys($multiple_organizations))) {
            if ($entity->get('master')->value) {
              // 是主数据, 获得 $account 所在的多组织
              $multiple_organizations = $person_manager->personMultipleOrganizations($multiple_organization_classification, $person->id());
              if (!in_array($entity->get($multiple_organization_classification)->target_id, array_keys($multiple_organizations))) {
                return AccessResult::forbidden();
              }
            }
            else {
              // 不是主数据
              return AccessResult::forbidden();
            }
          }
        }
      }
    }

    return parent::checkAccess($entity, $operation, $account);
  }

}
