<?php

namespace Drupal\report\Plugin;

use Drupal\Component\Plugin\ConfigurableInterface;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\report\Entity\ReportInterface;

/**
 * Defines an interface for Report plugins.
 */
interface ReportPluginInterface extends PluginInspectionInterface, ConfigurableInterface, PluginFormInterface {

  /**
   * Builds and returns the renderable array for this report plugin.
   *
   * @return array
   *   A renderable array representing the content of the block.
   *
   * @see \Drupal\report\ReportViewBuilder
   */
  public function build(ReportInterface $entity, $view_mode = 'full');

}
