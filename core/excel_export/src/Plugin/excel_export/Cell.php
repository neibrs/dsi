<?php

namespace Drupal\excel_export\Plugin\excel_export;

use Drupal\excel_export\Entity\ExcelExportInterface;
use Drupal\excel_export\Plugin\ExcelExportPluginBase;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * @ExcelExport(
 *   id = "cell",
 *   title = @Translation("Cell")
 * )
 */
class Cell extends ExcelExportPluginBase {

  /**
   * {@inheritdoc}
   */
  public function process(ExcelExportInterface $excel_export, Spreadsheet $sheet) {
    $cell = $this->configuration['cell'];
    if (!$cell) {
      if ($column = $this->configuration['column']) {
        if ($column == 'down') {
          $column = $sheet->getCell($sheet->getActiveCell())->getColumn();
          $column++;
        }
      }
      else {
        $column = $sheet->getCell($sheet->getActiveCell())->getColumn();
      }

      if ($row = $this->configuration['row']) {
        if ($row == 'down') {
          $row = $sheet->getCell($sheet->getActiveCell())->getRow();
          $row++;
        }
      }
      else {
        $row = $sheet->getCell($sheet->getActiveCell())->getRow();
      }

      $cell = $column . $row;
    }

    $sheet->setCellValue($cell, $this->buildValue($excel_export));

    if ($this->configuration['merge_to']) {
      $sheet->mergeCells($cell . ':' . $this->configuration['merge_to']);
    }
  }

  protected function buildValue(ExcelExportInterface $excel_export) {
    return $this->configuration['content'];
  }

}
