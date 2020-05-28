<?php

namespace Drupal\views_template;

use Drupal\views\ViewEntityInterface;

interface ViewsTemplateManagerInterface {

  /**
   * @return array;
   */
  public function getViewOverride(ViewEntityInterface $view);

  public function setViewOverride(ViewEntityInterface $view, $override);

}
