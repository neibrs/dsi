<?php

namespace Drupal\organization;

use Drupal\layout_builder\Section;
use Drupal\layout_builder\SectionComponent;

class OrganizationManager implements OrganizationManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function getOrganizationSection() {
    $section = new Section('layout_twocol_section', [
      'column_widths' => '25-75',
    ], [
      'organization' => new SectionComponent('organization', 'first', [
        'id' => 'organization_tree_switch',
        'label' => 'Organization tree switch',
        'label_display' => FALSE,
      ]),
    ]);

    return $section;
  }

}
