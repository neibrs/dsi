<?php

namespace Drupal\excel_export\Plugin;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;

class ExcelExportPluginManager extends DefaultPluginManager {

  /**
   * {@inheritdoc}
   */
  public function __construct(\Traversable $namespaces, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/excel_export', $namespaces, $module_handler, NULL, 'Drupal\excel_export\Annotation\ExcelExport');
  }

}
