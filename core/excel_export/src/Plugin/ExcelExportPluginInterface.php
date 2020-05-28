<?php

namespace Drupal\excel_export\Plugin;

use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\excel_export\Entity\ExcelExportInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

interface ExcelExportPluginInterface extends PluginInspectionInterface {

  public function process(ExcelExportInterface $excel_export, Spreadsheet $sheet);

}
