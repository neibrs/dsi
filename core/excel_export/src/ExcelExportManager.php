<?php

namespace Drupal\excel_export;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\excel_export\Entity\ExcelExportInterface;
use Drupal\excel_export\Plugin\ExcelExportPluginManager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelExportManager implements ExcelExportManagerInterface {

  /**
   * @var \Drupal\excel_export\Plugin\ExcelExportPluginManagerInterface
   */
  protected $excelExportPluginManager;

  public function __construct(PluginManagerInterface $excel_export_plugin_manager) {
    $this->excelExportPluginManager = $excel_export_plugin_manager;
  }

  public function export(ExcelExportInterface $excel_export) {
    $wrapper = \Drupal::service('stream_wrapper_manager')->getViaUri('private://export/');
    $filename = $excel_export->id();
    foreach ($excel_export->getParameters() as $key => $value) {
      if ($key != 'destination') {
        $filename .= '-' . $value;
      }
    }
    $filename .= '.xlsx';
    $path = $wrapper->realpath() . '/' . $filename;

    $xls = new Spreadsheet();

    $sheets = $excel_export->getSheets();
    $first = TRUE;
    foreach ($sheets as $sheet) {
      if ($first) {
        $work_sheet = $xls->getActiveSheet();
        $first = FALSE;
      }
      else {
        $work_sheet = $xls->createSheet();
      }
      $work_sheet->setTitle($sheet['title']);

      foreach ($sheet['process'] as $process) {
        $plugin = $this->excelExportPluginManager->createInstance($process['plugin'], $process);
        $plugin->process($excel_export, $work_sheet);
      }
    }

    $writer = new Xlsx($xls);
    $writer->save($path);

    return $wrapper->getUri() . $filename;
  }

}
