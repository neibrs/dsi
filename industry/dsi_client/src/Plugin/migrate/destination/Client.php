<?php

namespace Drupal\dsi_client\Plugin\migrate\destination;

use Drupal\dsi_record\Entity\Record;
use Drupal\import\Plugin\migrate\destination\EntityContentBase;
use Drupal\migrate\Row;

/**
 * @MigrateDestination(
 *   id = "entity:dsi_client",
 * )
 */
class Client extends EntityContentBase {
  
  /**
   * {@inheritdoc}
   */
  public function import(Row $row, array $old_destination_id_values = []) {
    // TODO, 当前client需要从回访记录里面获取最近的时间，设置到client修改时间作为客户的最近跟进时间
    $ids = parent::import($row, $old_destination_id_values);
    

    $queue = \Drupal::queue('client_record_process');
    $queue->createItem(['ids' => $ids, 'row' => $row]);
    return $ids;
  }
  
}