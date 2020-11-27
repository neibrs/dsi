<?php

namespace Drupal\alert;

use Drupal\Core\Entity\Sql\SqlContentEntityStorage;

/**
 * Defines the storage handler class for nodes.
 *
 * This extends the base storage class, adding required special handling for
 * alert entities.
 */
class AlertStorage extends SqlContentEntityStorage implements AlertStorageInterface {

}
