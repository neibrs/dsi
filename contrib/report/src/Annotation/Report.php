<?php

namespace Drupal\report\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Report item annotation object.
 *
 * @see \Drupal\report\Plugin\ReportManager
 * @see \Drupal\report\Plugin\ReportPluginInterface
 *
 * @Annotation
 */
class Report extends Plugin {


  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   *
   * @ingroup plugin_translatable
   */
  public $label;

  /**
   * An array of entity types the report supports.
   *
   * @var array
   */
  public $entity_types = [];

}
