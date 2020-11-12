<?php

namespace Drupal\import;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Overrides the plugin.manager.migration.
 */
class ImportServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('plugin.manager.migration');
    $definition->setClass('\Drupal\import\Plugin\MigrationPluginManager');
  }
}