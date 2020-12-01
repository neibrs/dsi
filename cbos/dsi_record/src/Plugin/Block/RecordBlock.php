<?php

namespace Drupal\dsi_record\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a 'RecordBlock' block.
 *
 * @Block(
 *  id = "dsi_record_block",
 *  admin_label = @Translation("Record block"),
 * )
 */
class RecordBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'dsi_record_block';

    $record_storage = \Drupal::entityTypeManager()->getStorage('dsi_record');
    $query = $record_storage->getQuery();

    // 1. 过期未完成事件
    $query_expired = $query->condition('state', FALSE);
    $expired_ids = $query_expired->execute();

    $rows = array_map(function ($item) {
      $label = '';
      if ($item->get('entity_type')->value) {
        $entity_type = \Drupal::entityTypeManager()->getStorage($item->get('entity_type')->value)->getEntityType();
        $label .= $entity_type->getLabel() . '-';
      }
      return $label . $item->label();
    }, $record_storage->loadMultiple($expired_ids));
    $data = [
      'expired' => [
        'count' => count($expired_ids),
        'data' => $rows,
      ],
    ];
    // 2. 即将过期事件
    // 3. 本周未完成事件
    //    $query_week = $query->condition('state', FALSE)
    //      ->condition('start', \DateTime::createFromFormat())
    $build['#content']['expired_data'] = $data['expired'];
    $build['#attached']['library'][] = 'dsi_record/dsi_record.popover';

    return $build;
  }

}
