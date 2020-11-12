<?php

namespace Drupal\organization\Plugin\views\cache;

use Drupal\Core\Cache\Cache;
use Drupal\views_plus\Plugin\views\cache\TagWithDisplayTags;

/**
 * employee assignment caching of query results for Views displays.
 *
 * @ingroup views_cache_plugins
 *
 * @ViewsCache(
 *   id = "employee_assignment_tag",
 *   title = @Translation("Employee assignment tag based"),
 *   help = @Translation("Tag based caching of data. Caches will persist until any related cache tags are invalidated.")
 * )
 */

class EmployeeAssignment extends TagWithDisplayTags {
  
  public function getCacheTags() {
    $tags = parent::getCacheTags();
    
    if (\Drupal::moduleHandler()->moduleExists('employee_assignment')) {
      $employee_assignment_list_cache_tags = \Drupal::entityTypeManager()->getDefinition('employee_assignment')->getListCacheTags();
      
      return Cache::mergeTags($employee_assignment_list_cache_tags, $tags);
    }
    
    return $tags;
  }
}