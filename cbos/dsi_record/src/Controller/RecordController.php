<?php

namespace Drupal\dsi_record\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\layout_builder\Section;

/**
 * Returns responses for record routes.
 */
class RecordController extends ControllerBase {

  public function getRecords() {

    $section = \Drupal::service('dsi_record.manager')->getRecordSection();

    $build = $section->toRenderArray();

    $build['second']['calendar'] = views_embed_view('record_calendar', 'page_1');
    $build['second']['calendar']['#prefix'] = '<div class="card"><div class="card-content">';
    $build['second']['calendar']['#suffix'] = '</div></div>';

    return $build;
  }

  public function getRecordsByEntity($entity_type, $entity_id) {
    $section = new Section('layout_twocol_section', [
      'column_widths' => '50-50',
    ]);
    $build = $section->toRenderArray();
    $build['second']['record'] = \Drupal::service('plugin.manager.block')->createInstance('record_by_entity_block', [
      'entity_type' => $entity_type,
      'entity_id' => $entity_id,
    ])->build();

    $context1 = [
      'entity_type' => $entity_type,
      'entity_id' => $entity_id,
    ];
    \Drupal::moduleHandler()->alter('record_entity_list', $build, $context1);

    return $build;
  }

  public function setStatus($entity_id,$state)
  {
//    /* @var \Drupal\dsi_record\Entity\Record $entity */
//    if (!empty($entity_id) && !empty($status)){
//      $entity = $entity->load($entity_id);
//      $entity->status = $status;
//      //操作
//      if ($entity->save()){
//          //redirect  set->massage
//      }
//    }
//    return ['code'=>400,'massage'=>'未知错误'];

    return ['entity_id'=>$entity_id,'status'=>$state];
  }

}
