<?php

namespace Drupal\dsi_record\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;
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
    $build['second']['record'] = \Drupal::service('plugin.manager.block')->createInstance('record_by_entity_block',[
      'entity_type' => $entity_type,
      'entity_id' => $entity_id
    ])->build();

    $context1 = [
      'entity_type' => $entity_type,
      'entity_id' => $entity_id,
    ];
    \Drupal::moduleHandler()->alter('record_entity_list', $build, $context1);

    return $build;
  }

  public function getJsonRecordsByEntity($entity_type, $entity_id) {
    
    $data = [
      'entity_type' => $entity_type,
      'entity_id' => $entity_id,
    ];
    return JsonResponse::create($data);
  }
}
