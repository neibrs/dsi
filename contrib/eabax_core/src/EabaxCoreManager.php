<?php

namespace Drupal\eabax_core;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Menu\MenuActiveTrailInterface;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\Core\Menu\MenuTreeParameters;

class EabaxCoreManager implements EabaxCoreManagerInterface {

  /**
   * The active menu trail service.
   *
   * @var \Drupal\Core\Menu\MenuActiveTrailInterface
   */
  protected $menuActiveTrail;

  /**
   * The menu link tree manager.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menuLinkTree;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(MenuActiveTrailInterface $menu_active_trail, MenuLinkTreeInterface $menu_link_tree, EntityTypeManagerInterface $entity_type_manager) {
    $this->menuActiveTrail = $menu_active_trail;
    $this->menuLinkTree = $menu_link_tree;
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * Loads the contents of a menu block.
   *
   * @return array
   *   A render array suitable for
   *   \Drupal\Core\Render\RendererInterface::render().
   *
   * @see \Drupal\system\SystemManager::getBlockContents()
   */
  public function getMenuBlockContents($menu_id) {
    $link = $this->menuActiveTrail->getActiveLink($menu_id);
    if ($link && $content = \Drupal::service('system.manager')->getAdminBlock($link)) {
      $output = [
        '#theme' => 'admin_block_content',
        '#content' => $content,
      ];
    }
    elseif ($content = $this->getMenuBlock($menu_id)) {
      $output = [
        '#theme' => 'admin_block_content',
        '#content' => $content,
      ];
    }
    else {
      $output = [
        '#markup' => t('You do not have any menu items.'),
      ];
    }
    return $output;
  }

  /**
   * Provide a single block on the administration overview page.
   *
   * @return array
   *   An array of menu items, as expected by admin-block-content.html.twig.
   *
   * @see \Drupal\system\SystemManager::getAdminBlock()
   */
  public function getMenuBlock($menu_id) {
    $content = [];
    $parameters = new MenuTreeParameters();
    $parameters->setTopLevelOnly()->onlyEnabledLinks();
    $tree = $this->menuLinkTree->load($menu_id, $parameters);
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $this->menuLinkTree->transform($tree, $manipulators);
    $step = 1;
    foreach ($tree as $key => $element) {
      // Only render accessible links.
      if (!$element->access->isAllowed()) {
        // @todo Bubble cacheability metadata of both accessible and
        //   inaccessible links. Currently made impossible by the way admin
        //   blocks are rendered.
        continue;
      }

      /** @var $link \Drupal\Core\Menu\MenuLinkInterface */
      $link = $element->link;
      $content[$key]['title'] = $link->getTitle();
      $content[$key]['options'] = $link->getOptions();
      $content[$key]['description'] = $link->getDescription();
      $content[$key]['url'] = $link->getUrlObject();

      $step++;
    }
    return $content;
  }

  public function setupRoleMenu($role_id) {
    $menu_id = 'role-menu-' . $role_id;
    if ($menu = $this->entityTypeManager->getStorage('menu')->load($menu_id)) {
      return;
    }

    /** @var \Drupal\user\RoleInterface $role */
    $role = $this->entityTypeManager->getStorage('user_role')->load($role_id);

    // Create the role menu.
    $menu = $this->entityTypeManager->getStorage('menu')
      ->create([
        'id' => $menu_id,
        'label' => $role->label(),
        'description' => t('Role menu'),
        'locked' => TRUE,
      ]);
    $menu->save();

    // Setup the role menu.
    $role->setThirdPartySetting('role_menu', 'menu', $menu_id);
    $role->save();
  }
}
