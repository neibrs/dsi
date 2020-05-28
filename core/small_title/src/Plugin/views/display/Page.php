<?php

namespace Drupal\small_title\Plugin\views\display;

use Drupal\views\Plugin\views\display\Page as PageBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Add _small_title_callback support to Page plugin.
 */
class Page extends PageBase {

  public function alterRoutes(RouteCollection $collection) {
    $view_route_names = [];
    $view_path = $this->getPath();
    $view_id = $this->view->storage->id();
    $display_id = $this->display['id'];
    $view_route = $this->getRoute($view_id, $display_id);

    foreach ($collection->all() as $name => $route) {
      if ($this->overrideApplies($view_path, $view_route, $route)) {
        $parameters = $route->compile()->getPathVariables();

        // @todo Figure out whether we need to merge some settings (like
        // requirements).

        // Replace the existing route with a new one based on views.
        $original_route = $collection->get($name);
        $collection->remove($name);

        $path = $view_route->getPath();
        // Replace the path with the original parameter names and add a mapping.
        $argument_map = [];
        // We assume that the numeric ids of the parameters match the one from
        // the view argument handlers.
        foreach ($parameters as $position => $parameter_name) {
          $path = str_replace('{arg_' . $position . '}', '{' . $parameter_name . '}', $path);
          $argument_map['arg_' . $position] = $parameter_name;
        }
        // Copy the original options from the route, so for example we ensure
        // that parameter conversion options is carried over.
        $view_route->setOptions($view_route->getOptions() + $original_route->getOptions());

        if ($original_route->hasDefault('_title_callback')) {
          $view_route->setDefault('_title_callback', $original_route->getDefault('_title_callback'));
        }
        // Add _small_title_callback support
        if ($original_route->hasDefault('_small_title_callback')) {
          $view_route->setDefault('_small_title_callback', $original_route->getDefault('_small_title_callback'));
        }

        // Set the corrected path and the mapping to the route object.
        $view_route->setOption('_view_argument_map', $argument_map);
        $view_route->setPath($path);

        $collection->add($name, $view_route);
        $view_route_names[$view_id . '.' . $display_id] = $name;
      }
    }

    return $view_route_names;
  }

}
