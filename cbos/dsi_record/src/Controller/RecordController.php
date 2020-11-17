<?php

namespace Drupal\dsi_record\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Returns responses for record routes.
 */
class RecordController extends ControllerBase {

  public function getRecords() {

    $section = \Drupal::service('dsi_record.manager')->getRecordSection();

    $build = $section->toRenderArray();

    $build['second']['calendar'] = views_embed_view('record_calendar', 'page_1');
    $build['second']['calendar']['#prefix'] = '<div class="card"><div class=""card-content">';
    $build['second']['calendar']['#suffix'] = '</div></div>';

    return $build;
  }

}
