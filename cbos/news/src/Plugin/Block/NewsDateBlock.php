<?php
namespace Drupal\news\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Cache\Cache;
use Drupal\Core\Url;
use Drupal\node\NodeInterface;

/**
 * Provides a block to display 'related news' elements.
 *
 * @Block(
 *   id = "news_date_block",
 *   admin_label = @Translation("News date block"),
 * )
 */
class NewsDateBlock extends BlockBase {

  /**
   * {@inheritDoc}
   */
  public function build() {
    $build = [];
    $build['#theme'] = 'news_date_block';
    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node instanceof NodeInterface) {
      $build['#content'] = [
        'date' => date('Y m-d', $node->get('created')->value),
        'category' => '',//$node->field_category->value,
      ];
    }
    return $build;
  }

}
