<?php

namespace Drupal\views_plus\Plugin\views\cache;

use Drupal\Core\Cache\Cache;
use Drupal\views\Plugin\views\cache\Tag;

/**
 * @ViewsCache(
 *   id = "tag_with_display_tags",
 *   title = @Translation("Tag based (display)"),
 * )
 */
class TagWithDisplayTags extends Tag {

  /**
   * {@inheritdoc}
   */
  public function getCacheTags() {
    $tags = parent::getCacheTags();

    if (isset($this->view->current_display)) {
      $currentDisplay = $this->view->storage->getDisplay($this->view->current_display);
      if (isset($currentDisplay['cache_metadata']['tags'])) {
        $tags = Cache::mergeTags($tags, $currentDisplay['cache_metadata']['tags']);
      }
    }

    return $tags;
  }

}
