<?php

namespace Drupal\views_template;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Cache\CacheableDependencyInterface;
use Drupal\views\ViewEntityInterface;
use Drupal\views\ViewExecutable;

class ViewsTemplateManager implements ViewsTemplateManagerInterface {

  /**
   * {@inheritdoc}
   */
  public function getViewOverride($view) {
    if (is_string($view)) {
      $view = \Drupal::entityTypeManager()->getStorage('view')->load($view);
    }

    $view_override = \Drupal::service('tempstore.private')->get('views_template.override')->get($view->id());
    if (!$view_override) {
      $view_override = [];
      $view_template_id = $this->getViewTemplate($view->id());
      if (!empty($view_template_id)) {
        $view_template = \Drupal::entityTypeManager()->getStorage('view_template')->load($view_template_id);
        if ($view_template) {
          $view_override['fields'] = $view_template->getFields();
          $view_override['filters'] = $view_template->getFilters();
        }
      }
      else {
        $display = $view->getDisplay('default');
        $view_override['fields'] = $display['display_options']['fields'];
        $view_override['filters'] = [];
      }
    }

    \Drupal::moduleHandler()->alter('view_override', $view_override, $view);

    return $view_override;
  }

  /**
   * {@inheritdoc}
   */
  public function setViewOverride($view, $override) {
    if (is_string($view)) {
      $view = \Drupal::entityTypeManager()->getStorage('view')->load($view);
    }

    \Drupal::moduleHandler()->invokeAll('set_view_override', [$view, $override]);

    if ((!isset($override['filters']) || empty($override['filters'])) && (!isset($override['fields']) || empty($override['fields']))) {
      \Drupal::service('tempstore.private')->get('views_template.override')->delete($view->id());
    }
    else {
      \Drupal::service('tempstore.private')->get('views_template.override')->set($view->id(), $override);
    }

    // TODO
    // Clear page cache for view.
    Cache::invalidateTags($view->getCacheTags());
  }

  /**
   * {@inheritdoc}
   */
  public function deleteFieldsOverride($view_id) {
    if (is_object($view_id)) {
      $view_id = $view_id->id();
    }

    $override = $this->getViewOverride($view_id);
    unset($override['fields']);
    $this->setViewOverride($view_id, $override);
  }
  
  /**
   * 获取当前用户特定视图的template_id.
   *
   * @param string $view_id
   *   视图ID.
   *
   * @return string|null
   *   视图的template_id.
   */
  public function getViewTemplate($view_id) {
    $view_template = \Drupal::service('tempstore.private')->get('current_view_template')->get($view_id);

    \Drupal::moduleHandler()->alter('view_template', $view_template, $view_id);
    return $view_template;
  }
  
  /**
   * 设置当前用户特定视图的template.
   */
  public function setViewTemplate($view, $view_template_id) {
    if (!is_object($view)) {
      $view = \Drupal::entityTypeManager()->getStorage('view')->load($view);
    }

    \Drupal::moduleHandler()->invokeAll('set_view_template', ['view' => $view, $view_template_id => $view_template_id]);

    if (!$view_template_id) {
      \Drupal::service('tempstore.private')->get('current_view_template')->delete($view->id());
    }
    else {
      \Drupal::service('tempstore.private')->get('current_view_template')->set($view->id(), $view_template_id);
    }
    \Drupal::service('tempstore.private')->get('views_template.override')->delete($view->id());
    
    Cache::invalidateTags($view->getCacheTags());
  }
  
  public function applyOverridesToView(ViewExecutable $view, $override) {
    if (empty($override)) {
      return;
    }
  
    $default = $view->displayHandlers->get('default');
    $display_id = $view->current_display; /* Check it.*/
    
    /* Set fields */
    if (isset($override['fields']) && !empty($override['fields'])) {
      $default->options['fields'] = $override['fields'];
    
      foreach ($override['fields'] as $id => $field) {
        // 删除 rest_export 不需要的字段.
        // @see views_plus_view_presave()
        if ($display_id == 'rest_export_1') {
          if (in_array($id, [$field['table'] . '_bulk_form', 'operations'], TRUE)) {
            unset($default->options['fields'][$id]);
            continue;
          }
        }
      
        /* 添加缓存。*/
        $handler = \Drupal::service('plugin.manager.views.field')->getHandler($field);
        $handler->view = $view;
        $handler->displayHandler = $default;
        if ($handler instanceof CacheableDependencyInterface) {
          $view->storage->addCacheTags($handler->getCacheTags());
        }

        if (isset($default->options['style']['options']['columns'])) {
          /* 添加到 columns 设置，以提供排序排序功能。*/
          if (!in_array($id, $default->options['style']['options']['columns'], TRUE)) {
            if (isset($field['exclude']) && $field['exclude']) {
              continue;
            }
            if ($field['plugin_id'] == 'bulk_form') {
              continue;
            }

            $default->options['style']['options']['columns'][$id] = $id;

            $default->options['style']['options']['info'][$id] = [
              'sortable' => TRUE,
              'default_sort_order' => 'asc',
            ];
          }
        }
      }
    
      \Drupal::service('entity_filter.manager')->addHandlersRelationshipToDisplay($default, $override['fields']);
    }
  
    // Add filters.
    if (isset($override['filters'])) {
      foreach ($override['filters'] as $id => $filter) {
        $default->options['filters'][$id] = $filter;
        // Fix Undefined index: id
        $default->options['filters'][$id]['id'] = $id;
        
        // TODO: 添加缓存
      }
    
      // 将 filters 用到的 relationship 添加到 display options 里.
      \Drupal::service('entity_filter.manager')->addHandlersRelationshipToDisplay($default, $override['filters']);
    }
  }
  
}
