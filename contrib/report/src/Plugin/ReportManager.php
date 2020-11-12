<?php

namespace Drupal\report\Plugin;

use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;

/**
 * Provides the Report plugin manager.
 */
class ReportManager extends DefaultPluginManager {

  /**
   * Constructs a new ReportManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/Report', $namespaces, $module_handler, 'Drupal\report\Plugin\ReportPluginInterface', 'Drupal\report\Annotation\Report');

    $this->alterInfo('report_info');
    $this->setCacheBackend($cache_backend, 'report_plugins');
  }

}
