<?php
namespace Drupal\news\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a block to display 'related news' elements.
 *
 * @Block(
 *   id = "news_related_block",
 *   admin_label = @Translation("Related news block"),
 * )
 */
class NewsRelatedBlock extends BlockBase {

  public function build() {
    $build = [];
    $build['#theme'] = 'news_related_block';
    return $build;
  }

}
