<?php

namespace Drupal\excel_export\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a excel export annotation object.
 *
 * Plugin namespace: Plugin\excel_export
 *
 * @see \Drupal\excel_export\Plugin\ExcelExportPluginBase
 * @see \Drupal\excel_export\Plugin\ExcelExportPluginInterface
 * @see \Drupal\excel_export\Plugin\ExcelExportPluginManager
 * @see plugin_api
 *
 * @Annotation
 */
class ExcelExport extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The title of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $title;

}
