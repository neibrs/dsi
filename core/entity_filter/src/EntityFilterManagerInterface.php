<?php

namespace Drupal\entity_filter;

use Drupal\Core\Url;

interface EntityFilterManagerInterface {

  /**
   * @return array
   *   The render array.
   */
  public function buildFiltersDisplayForm($filters, Url $url);

}
