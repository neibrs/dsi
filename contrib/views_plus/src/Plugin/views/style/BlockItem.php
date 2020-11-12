<?php

namespace Drupal\views_plus\Plugin\views\style;

use Drupal\views\Plugin\views\style\HtmlList;
use Drupal\views\Plugin\views\style\StylePluginBase;

/**
 * Style plugin to render each item in an ordered or unordered list.
 *
 * @ingroup views_style_plugins
 *
 * @ViewsStyle(
 *   id = "block_item",
 *   title = @Translation("Block Item"),
 *   help = @Translation("Displays rows as block item."),
 *   theme = "views_view_block_item",
 *   display_types = {"normal"}
 * )
 */
class BlockItem extends HtmlList {

}