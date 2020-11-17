<?php

namespace Drupal\dsi_record;

use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;

class RecordManager implements RecordManagerInterface {

  public function getRecordSection() {
    $section = new Section('layout_twocol_section', [
      'column_widths' => '25-75',
    ], [
      'record' => new SectionComponent('record', 'first', [
        'id' => 'dsi_record_block',
        'label' => 'Record block',
        'label_display' => FALSE,
      ]),
    ]);

    return $section;
  }

}
