<?php

namespace Drupal\dsi_record\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\dsi_record\Entity\Record;
use Drupal\layout_builder\Section;
use Symfony\Component\HttpFoundation\JsonResponse;

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

  /**
   * @param $entity_id
   * @param $state
   *
   * @return false|string
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function setStatus($entity_id,$state) {
    if (!empty($entity_id) and isset($state)){
      $record = Record::load($entity_id);
      $record->state = $state==0 ? FALSE : TRUE;
      //操作
      $data = [
        'code'=>4000,
        'entity_id'=>0,
        'massage'=>'未知错误'
      ];
      if ($record->save()){
        $data = ['code'=>200,'entity_id'=>$entity_id,'massage'=>'操作成功'];
      }
      return new JsonResponse($data);
    }

  }

}
