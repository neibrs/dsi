<?php

namespace Drupal\views_template;

use Drupal\views\ViewExecutable;

interface ViewsTemplateManagerInterface {

  /**
   * @return array;
   */
  public function getViewOverride($view);

  public function setViewOverride($view_id, $override);

  public function deleteFieldsOverride($view_id);

  public function applyOverridesToView(ViewExecutable $view, $overrides);
  
}
