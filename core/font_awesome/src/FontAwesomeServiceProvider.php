<?php

namespace Drupal\font_awesome;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class FontAwesomeServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('plugin.manager.menu.local_action');
    $definition->setClass('Drupal\font_awesome\Menu\LocalActionManager');
  }

}
