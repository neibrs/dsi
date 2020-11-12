<?php

namespace Drupal\views_plus;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class ViewsPlusServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('views.views_data_helper');
    $definition->setClass('Drupal\views_plus\ViewsDataHelper');
  }

}
