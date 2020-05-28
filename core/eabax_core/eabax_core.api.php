<?php

/**
 * ALter the title switches.
 *
 * @see eabax_core_block_view_page_title_block_alter()
 */
function hook_title_switch_alter(array &$title_switches, array $route_parameters) {
  if (isset($route_parameters['person'])) {
    $title_switches['person'] = \Drupal::service('form_builder')->getForm('\Drupal\person\Form\OrganizationPersonSwitchForm', $route_parameters['person']);
  }
}
