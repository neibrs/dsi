<?php
namespace Drupal\news\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;

/**
 * Provides a block to display 'related news' elements.
 *
 * @Block(
 *   id = "news_related_block",
 *   admin_label = @Translation("Related news block"),
 * )
 */
class NewsRelatedBlock extends BlockBase {

  /**
   * {@inheritDoc}
   */
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

  /**
   * {@inheritDoc}
   */
  public function getCacheTags() {
    $news = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
      'type' => 'article',
    ]);
    if (!empty($news)) {
      $new = reset($news);
      $tags = $new->getCacheTags();
      return Cache::mergeTags(parent::getCacheTags(), $tags);
    }
  }
}
