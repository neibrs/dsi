<?php

namespace Drupal\Tests\eabax_core\Functional;

use Drupal\Core\Menu\MenuTreeParameters;

trait MenuPagesTestTrait {

  public function viewAllPagesOfMenu($menu_id, $check_link = TRUE) {
    $assert_session = $this->assertSession();

    /** @var \Drupal\Core\Menu\MenuLinkTreeInterface $menu_link_tree */
    $menu_link_tree = \Drupal::service('menu.link_tree');
    $tree = $menu_link_tree->load($menu_id, new MenuTreeParameters());
    $assert_session->assert(!empty($tree), 'Menu ' . $menu_id . ' has items.');
    foreach ($tree as $element) {
      if ($check_link) {
        $assert_session->linkExists($element->link->getTitle());
      }
      $this->drupalGet($element->link->getUrlObject());
      $assert_session->statusCodeEquals(200);
    }
  }

}
