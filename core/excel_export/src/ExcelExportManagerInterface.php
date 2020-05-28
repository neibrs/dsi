<?php

namespace Drupal\excel_export;

use Drupal\excel_export\Entity\ExcelExportInterface;

interface ExcelExportManagerInterface {

  /**
   * @return string
   */
  public function export(ExcelExportInterface $excel_export);

}
