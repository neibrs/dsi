<?php

namespace Drupal\views_plus\Element;

use Drupal\views\Element\View as ViewBase;
use Drupal\views\Views;

/**
 * 让 view 支持 override.
 */
class View extends ViewBase {

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    return parent::getInfo() + [
      '#overvide' => [],
    ];
  }

  /**
   * View element pre render callback.
   *
   * @see \Drupal\views\Element\View::preRenderViewElement()
   */
  public static function preRenderViewElement($element) {
    // Allow specific Views displays to explicitly perform pre-rendering, for
    // those displays that need to be able to know the fully built render array.
    if (!empty($element['#pre_rendered'])) {
      return $element;
    }

    if (!isset($element['#view'])) {
      $view = Views::getView($element['#name']);
    }
    else {
      $view = $element['#view'];
    }

    $element += $view->element;
    $view->element = &$element;
    // Mark the element as being prerendered, so other code like
    // \Drupal\views\ViewExecutable::setCurrentPage knows that its no longer
    // possible to manipulate the $element.
    $view->element['#pre_rendered'] = TRUE;

    if (isset($element['#response'])) {
      $view->setResponse($element['#response']);
    }

    if ($view && $view->access($element['#display_id'])) {
      // 应用 overrides
      if (!empty($element['#override'])) {
        \Drupal::service('views_template.manager')->applyOverridesToView($view, $element['#override']);
      }

      if (!empty($element['#embed'])) {
        $element['view_build'] = $view->preview($element['#display_id'], $element['#arguments']);
      }
      else {
        // Add contextual links to the view. We need to attach them to the dummy
        // $view_array variable, since contextual_preprocess() requires that they
        // be attached to an array (not an object) in order to process them. For
        // our purposes, it doesn't matter what we attach them to, since once they
        // are processed by contextual_preprocess() they will appear in the
        // $title_suffix variable (which we will then render in
        // views-view.html.twig).
        $view->setDisplay($element['#display_id']);
        // Add the result of the executed view as a child element so any
        // #pre_render elements for the view will get processed. A #pre_render
        // element cannot be added to the main element as this is already inside
        // a #pre_render callback.
        $element['view_build'] = $view->executeDisplay($element['#display_id'], $element['#arguments']);

        if (isset($element['view_build']['#title'])) {
          $element['#title'] = &$element['view_build']['#title'];
        }

        if (empty($view->display_handler->getPluginDefinition()['returns_response'])) {
          // views_add_contextual_links() needs the following information in
          // order to be attached to the view.
          $element['#view_id'] = $view->storage->id();
          $element['#view_display_show_admin_links'] = $view->getShowAdminLinks();
          $element['#view_display_plugin_id'] = $view->display_handler->getPluginId();
          views_add_contextual_links($element, 'view', $view->current_display);
        }
      }
      if (empty($view->display_handler->getPluginDefinition()['returns_response'])) {
        $element['#attributes']['class'][] = 'views-element-container';
        $element['#theme_wrappers'] = ['container'];
      }
    }

    return $element;
  }

}