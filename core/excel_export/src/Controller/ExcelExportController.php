<?php

namespace Drupal\excel_export\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\excel_export\Entity\ExcelExportInterface;
use Drupal\excel_export\ExcelExportManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 */
class ExcelExportController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var \Drupal\excel_export\ExcelExportManagerInterface
   */
  protected $excelExportManager;

  public function __construct(ExcelExportManagerInterface $excel_export_manager) {
    $this->excelExportManager = $excel_export_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('excel_export.manager')
    );
  }

  public function export(Request $request, ExcelExportInterface $excel_export) {
    $parameters = $request->query->all();
    if ($config = $excel_export->getParameters()) {
      $parameters += $config;
    }
    $excel_export->setParameters($parameters);

    $uri = $this->excelExportManager->export($excel_export);
    $url = file_create_url($uri);

    $request->query->remove('destination');
    return new RedirectResponse($url);
  }

}
