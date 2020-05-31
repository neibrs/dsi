<?php
namespace Drupal\news\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;

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
    $news = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'article',
    ]);
    $node_news = [];
    foreach ($news as $id => $new) {
      $node_news[$id] = [
        'link' => $new->toUrl(),
        'title' => $new->label(),
      ];
    }
    $build['#content'] = $node_news;
    return $build;
  }

}
