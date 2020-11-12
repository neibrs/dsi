<?php

namespace Drupal\person;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\Core\Access\AccessResult;

/**
 * Access controller for the Identification information entity.
 *
 * @see \Drupal\person\Entity\IdentificationInformation.
 */
class PersonInformationAccessControlHandler extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  protected function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    /** @var \Drupal\person\Entity\IdentificationInformationInterface $entity */

    switch ($operation) {
      case 'view':
        return $entity->getPerson()->access('view', $account, TRUE);

      case 'update':
      case 'delete':
        return $entity->getPerson()->access('update', $account, TRUE);
    }

    // Unknown operation, no opinion.
    return AccessResult::neutral();
  }

  /**
   * {@inheritdoc}
   */
  protected function checkCreateAccess(AccountInterface $account, array $context, $entity_bundle = NULL) {
    return AccessResult::allowedIfHasPermission($account, 'maintain persons');
  }


}
