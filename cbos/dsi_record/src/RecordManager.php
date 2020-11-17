<?php

namespace Drupal\dsi_record;

use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;

class RecordManager implements RecordManagerInterface {

  public function getRecordSection() {
    $section = new Section('layout_twocol_section', [
      'column_widths' => '25-75',
    ], [
      'views_calendar' => new SectionComponent('views_calender', 'second', [
        'id' => 'record_calender',
        'label' => 'Record calender',
        'label_display' => FALSE,
      ]),
    ]);

    return $section;
  }

}
