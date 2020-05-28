<?php

namespace Drupal\import\Plugin\migrate\source;

use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Row;
use Drupal\migrate_source_xls\Plugin\migrate\source\XlsSource;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

/**
 * @MigrateSource(
 *   id = "xls_plus"
 * )
 */
class XlsSourcePlus extends XlsSource {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    // ImportFormSelectSheet will make columns duplicate
    $columns = [];
    foreach ($this->configuration['columns'] as $column) {
      $columns = $columns + $column;
    }
    $columns = array_flip($columns);
    $sources = [];
    $i = 0;
    foreach ($columns as $key => $value) {
      $sources[$i] = [$value => $key];
      $i++;
    }
    $this->columns = $sources;
    // Invoke the private prepareColumns function again.
    $this->prepareColumns();

    // optional_keys设置了最佳的keys选择顺序。
    // 根据optional_keys设置寻找第一个存在的keys.
    if (isset($this->configuration['optional_keys'])) {
      foreach ($this->configuration['optional_keys'] as $key) {
        if (in_array($key, $this->columns)) {
          $this->configuration['keys'] = [$key];
          break;
        }
      }
    }
  }

  /**
   * Prepare columns.
   */
  protected function prepareColumns() {
    if (isset($this->configuration['override_columns_tag'])) {
      $this->columns = $this->configuration['override_columns'];
      $this->configuration['columns'] = $this->configuration['override_columns'];
    }

    $columns = [];
    $iterator = $this->file
      ->getActiveSheet()
      ->getRowIterator($this->configuration['header_row'], $this->configuration['header_row']);
    /** @var \PhpOffice\PhpSpreadsheet\Cell\Cell $cell */
    foreach ($iterator->current()->getCellIterator() as $cell) {
      $header = str_replace(["\n"], [""], trim($cell->getValue()));
      $cell_column = $cell->getColumn();
      if (!empty($header)) {
        foreach ($this->columns as $column) {
          if (!isset($column[$header])) {
            continue;
          }
          $columns[$cell_column] = $column[$header];
          break;
        }
        if (!isset($columns[$cell_column])) {
          $columns[$cell_column] = $header;
        }
      }
    }
    foreach ($this->columns as $column) {
      foreach ($column as $key => $value) {
        if (substr($key, 0, 1) == '_') {
          $columns[substr($key, 1)] = $value;
        }
      }
    }
    $this->columns = $columns;
  }

  /**
   * Fix RichText issue.
   */
  public function next() {
    $this->currentSourceIds = NULL;
    $this->currentRow = NULL;
    // In order to find the next row we want to process, we ask the source
    // plugin for the next possible row.
    while (!isset($this->currentRow) && $this->getIterator()->valid()) {
      /** @var \PhpOffice\PhpSpreadsheet\Worksheet\RowIterator $iterator */
      $row_data = []; $iterator = $this->getIterator();
      /** @var \PhpOffice\PhpSpreadsheet\Cell\Cell $cell */
      foreach ($iterator->current()->getCellIterator() as $cell) {
        if (isset($this->columns[$cell->getColumn()])) {
          $column = $this->columns[$cell->getColumn()];
          $value = $cell->getValue();

          // Convert RichText into PlainText
          if ($value instanceof RichText) {
            $value = $value->getPlainText();
          }

          $row_data[$column] = $value;
        }
      }
      $row_data = $row_data + $this->configuration;
      $row = new Row($row_data, $this->migration->getSourcePlugin()->getIds(), $this->migration->getDestinationIds());

      // Populate the source key for this row.
      $this->currentSourceIds = $row->getSourceIdValues();

      // Pick up the existing map row, if any, unless getNextRow() did it.
      if (!$this->mapRowAdded && ($id_map = $this->idMap->getRowBySource($this->currentSourceIds))) {
        $row->setIdMap($id_map);
      }

      // Clear any previous messages for this row before potentially adding
      // new ones.
      if (!empty($this->currentSourceIds)) {
        $this->idMap->delete($this->currentSourceIds, TRUE);
      }

      // Preparing the row gives source plugins the chance to skip.
      if ($this->prepareRow($row) === FALSE) {
        continue;
      }

      // Check whether the row needs processing.
      // 1. This row has not been imported yet.
      // 2. Explicitly set to update.
      // 3. The row is newer than the current highwater mark.
      // 4. If no such property exists then try by checking the hash of the row.
      if (!$row->getIdMap() || $row->needsUpdate() || $this->aboveHighwater($row) || $this->rowChanged($row)) {
        $this->currentRow = $row->freezeSource();
      }
      elseif (($id_map = $row->getIdMap())['destid1'] == NULL) {
        $this->messenger()->addWarning(t('If there are no other errors. You can force update it .Because some data is in a temporary table'));
      }
      $iterator->next();
    }
  }

}
