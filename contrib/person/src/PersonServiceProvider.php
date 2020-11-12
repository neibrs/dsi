<?php

namespace Drupal\person;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

class PersonServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    // Add employee user role to UserSession.
    $definition = $container->getDefinition('user.authentication.cookie');
    $definition->setClass('Drupal\person\Authentication\Provider\Cookie');
  }

}