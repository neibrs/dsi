<?php

namespace Drupal\views_plus\Plugin\views\display;

use Drupal\rest\Plugin\views\display\RestExport as RestExportBase;
use Drupal\views\Render\ViewsRenderPipelineMarkup;

class RestExport extends RestExportBase {

  public function render() {
    $build = parent::render();

    // 中文环境导出excel采用GBK字符集
    $build['#markup'] = iconv('UTF-8', 'GBK', $build['#markup']);

    if (!empty($this->view->live_preview)) {
      $build['#plain_text'] = $build['#markup'];
      unset($build['#markup']);
    }
    else {
      $build['#markup'] = ViewsRenderPipelineMarkup::create($build['#markup']);
    }

    return $build;
  }

}