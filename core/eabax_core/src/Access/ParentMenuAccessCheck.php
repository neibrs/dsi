<?php

namespace Drupal\eabax_core\Access;

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Routing\Access\AccessInterface;
use Drupal\Core\Session\AccountInterface;
use Symfony\Component\Routing\Route;

class ParentMenuAccessCheck implements AccessInterface {

  /**
   * @return \Drupal\Core\Access\AccessResultInterface
   *   The access result.
   */
  public function access(Route $route, AccountInterface $account) {
    $parent_menu_id = $route->getRequirement('_parent_menu_access');

    $menu_link_tree = \Drupal::service('menu.link_tree');
    $parameters = new MenuTreeParameters();
    $parameters->setRoot($parent_menu_id)->excludeRoot()->setTopLevelOnly()->onlyEnabledLinks();
    $tree = $menu_link_tree->load(NULL, $parameters);
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
    ];
    if (!empty($tree)) {
      $tree = $menu_link_tree->transform($tree, $manipulators);
    }

    return AccessResult::allowedIf(!empty($tree));
  }
}
