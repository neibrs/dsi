<?php

namespace Drupal\entity_filter;

use Drupal\Core\Url;
use Drupal\views\Plugin\views\display\DisplayPluginInterface;

interface EntityFilterManagerInterface {

  /**
   * @return array
   *   The render array.
   */
  public function buildFiltersDisplayForm($filters, Url $url);

  /**
   * @return string
   */
  public function getReadableFiltersString($base_table, $filters);
  
  /**
   * 将 fields/filters 里的关系添加到 display options 里.
   */
  public function addHandlersRelationshipToDisplay(DisplayPluginInterface $display, $handlers);

  /**
   * @return \Drupal\views\ViewExecutable
   */
  public function createView($base_table, $handlers_config);

}
