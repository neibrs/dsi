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
    
    // 从导入表中获取
    // 1. customer
    // 2. customer_contact_record
    $client_name = $row->getDestinationProperty('name');
    $query = \Drupal::database()->select('customer_contact_record', 'ccr');
    $query->leftJoin('customer', 'cu', 'ccr.customer_id = cu.customer_id');
    $query->fields('ccr', [
      'record_content',
      'record_start',
      'record_end',
      'created_at',
      'deleted_at'
    ]);
    $query->condition('cu.customer_name', $client_name);
    $result = $query->execute()->fetchAll();
    
    foreach ($result as $rs) {
      // create record
      $values = [
        'entity_type' => 'dsi_client',
        'entity_id' => $ids[0],
        'name' => substr(strip_tags($rs->record_content), 0, 32),
        'detail' => strip_tags($rs->record_content),
        'start' => strtotime($rs->record_start),
        'end' => strtotime($rs->record_end),
        'uid' => $row->getDestinationProperty('uid'),
        'state' => TRUE,
        'status' => TRUE,
        'created' => strtotime($rs->created_at),
        'changed' => strtotime($rs->updated_at),
      ];
      if (!empty($rs->deleted_at)) {
        $values['status'] = FALSE;
      }
      Record::create($values)->save();
    }
    
    return $ids;
  }
  
}