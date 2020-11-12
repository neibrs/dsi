<?php

namespace Drupal\eabax_core;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class EabaxCoreServiceProvider extends ServiceProviderBase {
  
  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('easy_breadcrumb.breadcrumb');
    $definition->setClass('Drupal\eabax_core\EasyBreadcrumbBuilder');
  }
  
}
