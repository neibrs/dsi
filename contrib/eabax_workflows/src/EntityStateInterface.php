<?php

namespace Drupal\eabax_workflows;

interface EntityStateInterface {

  public function getEntityControl();

  /**
   * Gets fields control for workflow state.
   */
  public function getFieldsControl();

  /**
   * Gets fields view  permission for workflow state.
   */
  public function getCanView($field, $default = TRUE);

  /**
   * Gets fields update permission for workflow state.
   */
  public function getCanUpdate($field, $default);

  /**
   * Gets fields value for workflow state.
   */
  public function getStatusSettingValue($field);

  /**
   * Gets fields settings for workflow state.
   */
  public function getStatusSetting($field, $default = 'not_used');

}
