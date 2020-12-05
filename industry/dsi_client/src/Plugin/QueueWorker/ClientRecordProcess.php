<?php
namespace Drupal\dsi_client\Plugin\QueueWorker;

use Drupal\Core\Queue\QueueWorkerBase;
use Drupal\dsi_record\Entity\Record;

/**
 * @QueueWorker(
 *   id = "client_record_process",
 *   title = @Translation("Client record process"),
 *   cron = {"time" = 60}
 * )
 */
class ClientRecordProcess extends QueueWorkerBase {
  
  /**
   * {@inheritDoc}
   */
  public function processItem($data) {
    // 从导入表中获取
    // 1. customer
    // 2. customer_contact_record
    $client_name = $data['row']->getDestinationProperty('name');
    $query = \Drupal::database()->select('customer_contact_record', 'ccr');
    $query->leftJoin('customer', 'cu', 'ccr.customer_id = cu.customer_id');
    $query->fields('ccr', [
      'record_content',
      'record_start',
      'record_end',
      'created_at',
      'updated_at',
      'deleted_at'
    ]);
    $query->condition('cu.customer_name', $client_name);
    $result = $query->execute()->fetchAll();
  
    $query_storage = \Drupal::entityTypeManager()->getStorage('dsi_record');
    foreach ($result as $rs) {
      $query_result = $query_storage->getQuery();
      $query_result->condition('name', mb_substr(strip_tags($rs->record_content), 0, 52));
      $query_result->condition('entity_type', 'dsi_client');
      $query_result->condition('entity_id', reset($data['ids']));
      $query_ids = $query_result->execute();
      $record = $query_storage->load(reset($data['ids']));
      if (!empty($query_ids) && !empty($record)) {
        // Update
        $values = [
          'detail' => [
            'value' => strip_tags($rs->record_content),
            'format' => 'basic_html',
          ],
          'start' => implode('T', explode(' ', $rs->record_start)),
          'end' => implode('T', explode(' ', $rs->record_end)),
          'user_id' => $data['row']->getDestinationProperty('follow'),
          //        'state' => TRUE,
          'status' => TRUE,
          'created' => strtotime($rs->created_at),
          'changed' => strtotime($rs->updated_at),
        ];
        foreach ($values as $key => $val) {
          $record->set($key, $val);
        }
        $record->save();
      }
      if (!empty($query_ids) && empty($record)) {
        // Create
        $values = [
          'entity_type' => 'dsi_client',
          'entity_id' => reset($data['ids']),
          'name' => mb_substr(strip_tags($rs->record_content), 0, 52),
          //'detail' => strip_tags($rs->record_content),
          'detail' => [
            'value' => strip_tags($rs->record_content),
            'format' => 'basic_html',
          ],
          'start' => implode('T', explode(' ',$rs->record_start)),
          'end' => implode('T', explode(' ',$rs->record_end)),
          'user_id' => $data['row']->getDestinationProperty('follow'),
          //'state' => TRUE,
          'status' => TRUE,
          'created' => strtotime($rs->created_at),
          'changed' => strtotime($rs->updated_at),
        ];
      
        if (!empty($rs->deleted_at)) {
          $values['status'] = FALSE;
        }
        $record = Record::create($values);
        $record->save();
      }
    }
  }
  
}
