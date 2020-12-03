<?php

namespace Drupal\dsi_record\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Routing\RouteMatch;

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
      return [
        'id'=>$item->id(),
        'name'=>$label . $item->label(),
        'date'=> $item->get('start')->getValue()[0]['value']
      ];
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
//    $build['#attached']['library'][] = 'dsi_record/dsi_record.ajax_set_status';
//dd($build);
    //4. 动态build Ajax
//    $build['checkbox-div']['#ajax'] = [
//       'callback' => '::ajaxChangeRecordStatus', // 事件回调 方法 || 类 (名)
//     ];

//    $build['link']['#attached']['drupalSettings']['ajax']['checkbox-div'] = [ //以触发元素ID做键名
//      'event'      => 'click',
////      'dialogType' => 'dialog', //该项使得显示位置在对话框中
//      'url'        => 'ajax/dsi_record/1/1/setStatus',
//      'method'     => 'append',
//      'effect'     => 'slide',
//      'speed'      => 1000,
//      'prevent'    => 'click',
//      'progress'   => [
//        'type'    => 'throbber',
//        'message' => '正在进行ajax...',
//      ],
//    ];
    return $build;
  }

  public function ajaxChangeRecordStatus()
  {
      //动态更新status


  }

}
