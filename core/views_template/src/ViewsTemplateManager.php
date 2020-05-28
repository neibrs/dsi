<?php

namespace Drupal\views_template;

use Drupal\Core\Cache\Cache;
use Drupal\views\ViewEntityInterface;

class ViewsTemplateManager implements ViewsTemplateManagerInterface {

  public function getViewOverride(ViewEntityInterface $view) {
    $view_override = \Drupal::service('tempstore.private')->get('views_template.override')->get($view->id());
    if (!$view_override) {
      $view_override = [];
      $view_template_id = $this->getViewTemplate($view->id());
      if (!empty($view_template_id)) {
        $view_template = \Drupal::entityTypeManager()->getStorage('view_template')->load($view_template_id);
        $view_override['fields'] = $view_template->getFields();
        $view_override['filters'] = $view_template->getFilters();
      }
      else {
        $display = $view->getDisplay('default');
        $view_override['fields'] = $display['display_options']['fields'];
        $view_override['filters'] = [];
      }
    }

    return $view_override;
  }

  public function setViewOverride(ViewEntityInterface $view, $override) {
    \Drupal::service('tempstore.private')->get('views_template.override')->set($view->id(), $override);
  }
  
  /**
   * 获取当前用户特定视图的template_id.
   *
   * @param string $view_id
   *   视图ID.
   *
   * @return string
   *   视图的template_id.
   */
  public function getViewTemplate($view_id) {
    return \Drupal::service('tempstore.private')->get('current_view_template')->get($view_id);
  }
  
  /**
   * 设置当前用户特定视图的template.
   */
  public function setViewTemplate($view, $view_template_id) {
    if (!is_object($view)) {
      $view = \Drupal::entityTypeManager()->getStorage('view')->load($view);
    }
    
    if (!$view_template_id) {
      \Drupal::service('tempstore.private')->get('current_view_template')->delete($view->id());
    }
    else {
      \Drupal::service('tempstore.private')->get('current_view_template')->set($view->id(), $view_template_id);
    }
    \Drupal::service('tempstore.private')->get('views_template.override')->delete($view->id());
    
    Cache::invalidateTags($view->getCacheTags());
  }
}
