<?php

namespace Drupal\dsi_ipa;

use Drupal\Core\Entity\ContentEntityStorageInterface;
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
interface IpaStorageInterface extends ContentEntityStorageInterface {

  /**
   * Gets a list of IP Address revision IDs for a specific IP Address.
   *
   * @param \Drupal\dsi_ipa\Entity\IpaInterface $entity
   *   The IP Address.
   *
   * @return int[]
   *   IP Address revision IDs (in ascending order).
   */
  public function revisionIds(IpaInterface $entity);

  /**
   * Gets a list of revision IDs having a given user as IP Address author.
   *
   * @param \Drupal\Core\Session\AccountInterface $account
   *   The user entity.
   *
   * @return int[]
   *   IP Address revision IDs (in ascending order).
   */
  public function userRevisionIds(AccountInterface $account);

}
