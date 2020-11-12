<?php

namespace Drupal\eabax_core\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Menu\MenuLinkTreeInterface;
use Drupal\locale\Locale;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Returns responses for eabax core routes.
 */
class EabaxCoreController extends ControllerBase {

  /**
   * The menu link tree manager.
   *
   * @var \Drupal\Core\Menu\MenuLinkTreeInterface
   */
  protected $menuLinkTree;

  public function __construct(MenuLinkTreeInterface $menu_link_tree) {
    $this->menuLinkTree = $menu_link_tree;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('menu.link_tree')
    );
  }

  /**
   * @param $menu_id
   * @return mixed
   */
  public function menuBlockPage($menu_id) {
    return \Drupal::service('eabax_core.manager')->getMenuBlockContents($menu_id);
  }

  /**
   * @param $menu_id
   * @return mixed
   */
  public function implementationMenuBlockPage($menu_id) {
    $output = \Drupal::service('eabax_core.manager')->getMenuBlockContents($menu_id);
    $output['#theme'] = 'admin_block_content__implementation';
    return $output;
  }
  
  /**
   * Ajax for sidebar toggle.
   */
  public function sidebarToggle() {
    $session = \Drupal::request()->getSession();
    $collapsed = $session->get('sidebar_collapsed', TRUE);
    $session->set('sidebar_collapsed', !$collapsed);
    
    return JsonResponse::create([]);
  }
}
