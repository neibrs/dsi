<?php

namespace Drupal\small_title;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Overrides the main_content_renderer.html service.
 */
class SmallTitleServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('title_resolver');
    $definition->setClass('Drupal\small_title\Controller\TitleResolver');

    // Set small title for page display.
    $definition = $container->getDefinition('main_content_renderer.html');
    $definition->setClass('Drupal\small_title\Render\MainContent\HtmlRenderer');

    // Remove buildTitle from EntityViewController::view
    $definition = $container->getDefinition('route_enhancer.entity');
    $definition->setClass('Drupal\small_title\Entity\Enhancer\EntityRouteEnhancer');
  }

}
