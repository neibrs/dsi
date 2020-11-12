<?php

namespace Drupal\entity_plus;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Overrides the access_check.entity_create service.
 */
class EntityPlusServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Add entity_type_id placeholder support for access checker.
    $definition = $container->getDefinition('access_check.entity_create');
    $definition->setClass('Drupal\entity_plus\Entity\EntityCreateAccessCheck');

    $definition = $container->getDefinition('access_check.entity');
    $definition->setClass('Drupal\entity_plus\Entity\EntityAccessCheck');

    $definition = $container->getDefinition('route_enhancer.entity');
    $definition->setClass('Drupal\entity_plus\Entity\Enhancer\EntityRouteEnhancer');

    $definition = $container->getDefinition('plugin.manager.field.formatter');
    $definition->setClass('Drupal\entity_plus\Field\FormatterPluginManager');

    if ($container->has('reference_table_formatter.renderer')) {
      $definition = $container->getDefinition('reference_table_formatter.renderer');
      $definition->setClass('Drupal\entity_plus\EntityToTableRenderer');
    }
  }

}
