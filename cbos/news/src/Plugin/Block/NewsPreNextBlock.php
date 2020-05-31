<?php
namespace Drupal\news\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Provides a block to display 'Pre next news' elements.
 *
 * @Block(
 *   id = "news_pre_next_block",
 *   admin_label = @Translation("News Prenext block"),
 * )
 */
class NewsPreNextBlock extends BlockBase {

  /**
   * {@inheritDoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'news_pre_next_block';
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      // TODO Add dynamic content.
      $build['#content'] = [
        'pre' => [
          'title' => $node->label(),
          'link' => $node->toUrl()->toRenderArray(),
        ],
        'next' => [
          'title' => $node->label(),
          'link' => $node->toUrl()->toRenderArray(),
        ],
      ];
    }
    return $build;
  }

}
