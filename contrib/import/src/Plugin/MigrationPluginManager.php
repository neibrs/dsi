<?php

namespace Drupal\import\Plugin;

use Drupal\migrate\Plugin\MigrationPluginManager as MigrationPluginManagerBase;

class MigrationPluginManager extends MigrationPluginManagerBase {

  protected $defaults = [
    'class' => '\Drupal\import\Plugin\Migration',
  ];
}