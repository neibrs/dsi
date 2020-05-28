<?php

namespace Drupal\report\Controller;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Link;
use Drupal\report\Entity\ReportInterface;

class ReportController extends ControllerBase {

  public function addPage() {
    $build = [
      '#theme' => 'entity_add_list',
    ];

    /** @var \Drupal\Component\Plugin\PluginManagerInterface $report_manager */
    $report_manager = \Drupal::service('plugin.manager.report');
    $plugins = $report_manager->getDefinitions();
    foreach ($plugins as $plugin) {
      $build['#bundles'][$plugin['id']] = [
        'label' => $plugin['label'],
        'description' => '',
        'add_link' => Link::createFromRoute($plugin['label'], 'entity.report.add_form', [
          'plugin' => $plugin['id'],
        ]),
      ];
    }

    return $build;
  }

  /**
   * @see \Drupal\report\Plugin\ReportPluginBase::build()
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   */
  public function ajaxStyle(ReportInterface $report, $style) {
    $response = new AjaxResponse();

    $build = $report->getPlugin()->$style($report);
    $response->addCommand(new ReplaceCommand('#report-' . $report->id(), $build));

    return $response;
  }

}
