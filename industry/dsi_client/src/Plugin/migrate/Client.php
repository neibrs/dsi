<?php

namespace Drupal\dsi_client\Plugin\migrate\destination;

use Drupal\migrate\Plugin\migrate\destination\EntityContentBase;
use Drupal\migrate\Row;

/**
 * @MigrateDestination(
 *   id = "entity:dsi_client",
 * )
 */
class Client extends EntityContentBase {

  /**
   * {@inheritDoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    if (empty($row->getDestinationProperty('user_id'))) {
      $user = \Drupal::service('person.manager')->getUserByPersonName('张月月');
      $row->setDestinationProperty('user_id', $user->id());
    }
    if (empty($row->getDestinationProperty('follow'))) {
      $user = \Drupal::service('person.manager')->getUserByPersonName('王平');
      $row->setDestinationProperty('user_id', $user->id());
    }
    return parent::import($row, $old_destination_id_values);
  }

}
