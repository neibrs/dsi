<?php

namespace Drupal\dsi_ipa;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\dsi_ipa\Entity\IpaInterface;

/**
 * Defines the storage handler class for IP Addresses.
 *
 * This extends the base storage class, adding required special handling for
 * IP Addresses.
 *
 * @ingroup dsi_ipa
 */
class IpaStorage extends SqlContentEntityStorage implements IpaStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(IpaInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {dsi_ipa_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {dsi_ipa_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

}
