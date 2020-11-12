<?php

namespace Drupal\entity_log\Plugin\views\cache;

use Drupal\Core\Cache\Cache;
use Drupal\views\Plugin\views\cache\Tag;

/**
 * Simple caching of query results for Views displays.
 *
 * @ingroup views_cache_plugins
 *
 * @ViewsCache(
 *   id = "entity_log_tag",
 *   title = @Translation("Entity log tag"),
 *   help = @Translation("Entity log tag based caching of data. Caches will persist until any related cache tags are invalidated.")
 * )
 *
 */
class EntityLogTag extends Tag {

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags[] = $this->view->argument['entity_type_id']->getValue() . ':' . $this->view->argument['entity_id']->getValue();

    return Cache::mergeTags($tags, parent::getCacheTags());
  }
}