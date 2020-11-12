<?php

namespace Drupal\dsi_device;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;
use Drupal\Core\Session\AccountInterface;
use Drupal\dsi_device\Entity\DeviceInterface;

/**
 * Defines the storage handler class for Devices.
 *
 * This extends the base storage class, adding required special handling for
 * Devices.
 *
 * @ingroup dsi_device
 */
class DeviceStorage extends SqlContentEntityStorage implements DeviceStorageInterface {

  /**
   * {@inheritdoc}
   */
  public function revisionIds(DeviceInterface $entity) {
    return $this->database->query(
      'SELECT vid FROM {dsi_device_revision} WHERE id=:id ORDER BY vid',
      [':id' => $entity->id()]
    )->fetchCol();
  }

  /**
   * {@inheritdoc}
   */
  public function userRevisionIds(AccountInterface $account) {
    return $this->database->query(
      'SELECT vid FROM {dsi_device_field_revision} WHERE uid = :uid ORDER BY vid',
      [':uid' => $account->id()]
    )->fetchCol();
  }

}
