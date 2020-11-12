<?php

namespace Drupal\dsi_device;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface DeviceStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of Device revision IDs for a specific Device.
   *
   * @param \Drupal\dsi_device\Entity\DeviceInterface $entity
   *   The Device entity.
   *
   * @return int[]
   *   Device revision IDs (in ascending order).
   */
  public function revisionIds(DeviceInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as Device author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   Device revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

}
