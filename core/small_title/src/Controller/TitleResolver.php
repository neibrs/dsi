<?php

namespace Drupal\small_title\Controller;

use Drupal\Core\Controller\TitleResolver as TitleResolverBase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

/**
 */
class TitleResolver extends TitleResolverBase {

  /**
   * {@inheritdoc}
   */
  public function getSmallTitle(Request $request, Route $route) {
    $route_title = NULL;
    // A dynamic title takes priority. Route::getDefault() returns NULL if the
    // named default is not set.  By testing the value directly, we also avoid
    // trying to use empty values.
    if ($callback = $route->getDefault('_small_title_callback')) {
      $callable = $this->controllerResolver->getControllerFromDefinition($callback);
      $arguments = $this->argumentResolver->getArguments($request, $callable);
      $route_title = call_user_func_array($callable, $arguments);
    }
    elseif ($title = $route->getDefault('_small_title')) {
      $options = [];
      if ($context = $route->getDefault('_small_title_context')) {
        $options['context'] = $context;
      }
      $args = [];
      if (($raw_parameters = $request->attributes->get('_raw_variables'))) {
        foreach ($raw_parameters->all() as $key => $value) {
          $args['@' . $key] = $value;
          $args['%' . $key] = $value;
        }
      }
      if ($title_arguments = $route->getDefault('_small_title_arguments')) {
        $args = array_merge($args, (array) $title_arguments);
      }

      // Fall back to a static string from the route.
      $route_title = $this->t($title, $args, $options);
    }
    return $route_title;
  }

}
