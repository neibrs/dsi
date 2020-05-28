<?php

namespace Drupal\small_title\Plugin\DisplayVariant;

use Drupal\block\Plugin\DisplayVariant\BlockPageVariant;
use Drupal\Core\Block\MainContentBlockPluginInterface;
use Drupal\Core\Block\MessagesBlockPluginInterface;
use Drupal\Core\Block\TitleBlockPluginInterface;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\small_title\Plugin\Block\SmallTitleBlockPluginInterface;

/**
 * @PageDisplayVariant(
 *   id = "small_title_page",
 *   admin_label = @Translation("Page with small title")
 * )
 */
class SmallTitlePageVariant extends BlockPageVariant {

  protected $small_title = '';

  public function setSmallTitle($title) {
    $this->small_title = $title;
    return $this;
  }

  public function build() {
    // Track whether blocks showing the main content and messages are displayed.
    $main_content_block_displayed = FALSE;
    $messages_block_displayed = FALSE;

    $build = [
      '#cache' => [
        'tags' => $this->blockListCacheTags,
      ],
    ];
    // Load all region content assigned via blocks.
    $cacheable_metadata_list = [];
    foreach ($this->blockRepository->getVisibleBlocksPerRegion($cacheable_metadata_list) as $region => $blocks) {
      /** @var $blocks \Drupal\block\BlockInterface[] */
      foreach ($blocks as $key => $block) {
        $block_plugin = $block->getPlugin();
        if ($block_plugin instanceof MainContentBlockPluginInterface) {
          $block_plugin->setMainContent($this->mainContent);
          $main_content_block_displayed = TRUE;
        }
        elseif ($block_plugin instanceof SmallTitleBlockPluginInterface) {
          $block_plugin->setTitle($this->title);
          $block_plugin->setSmallTitle($this->small_title);
        }
        elseif ($block_plugin instanceof MessagesBlockPluginInterface) {
          $messages_block_displayed = TRUE;
        }
        $build[$region][$key] = $this->blockViewBuilder->view($block);

        // The main content block cannot be cached: it is a placeholder for the
        // render array returned by the controller. It should be rendered as-is,
        // with other placed blocks "decorating" it. Analogous reasoning for the
        // title block.
        if ($block_plugin instanceof MainContentBlockPluginInterface || $block_plugin instanceof TitleBlockPluginInterface) {
          unset($build[$region][$key]['#cache']['keys']);
        }
      }
      if (!empty($build[$region])) {
        // \Drupal\block\BlockRepositoryInterface::getVisibleBlocksPerRegion()
        // returns the blocks in sorted order.
        $build[$region]['#sorted'] = TRUE;
      }
    }

    // If no block that shows the main content is displayed, still show the main
    // content. Otherwise the end user will see all displayed blocks, but not
    // the main content they came for.
    if (!$main_content_block_displayed) {
      $build['content']['system_main'] = $this->mainContent;
    }

    // If no block displays status messages, still render them.
    if (!$messages_block_displayed) {
      $build['content']['messages'] = [
        '#weight' => -1000,
        '#type' => 'status_messages',
      ];
    }

    // If any render arrays are manually placed, render arrays and blocks must
    // be sorted.
    if (!$main_content_block_displayed || !$messages_block_displayed) {
      unset($build['content']['#sorted']);
    }

    // The access results' cacheability is currently added to the top level of the
    // render array. This is done to prevent issues with empty regions being
    // displayed.
    // This would need to be changed to allow caching of block regions, as each
    // region must then have the relevant cacheable metadata.
    $merged_cacheable_metadata = CacheableMetadata::createFromRenderArray($build);
    foreach ($cacheable_metadata_list as $cacheable_metadata) {
      $merged_cacheable_metadata = $merged_cacheable_metadata->merge($cacheable_metadata);
    }
    $merged_cacheable_metadata->applyTo($build);

    return $build;
  }

}
