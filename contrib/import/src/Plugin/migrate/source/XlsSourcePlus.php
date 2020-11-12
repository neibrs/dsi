<?php

namespace Drupal\import\Plugin\migrate\source;

use Drupal\import\ImportTrait;
use Drupal\import\Row;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate_source_xls\Plugin\migrate\source\XlsSource;
use PhpOffice\PhpSpreadsheet\RichText\RichText;

/**
 * @MigrateSource(
 *   id = "xls_plus"
 * )
 */
class XlsSourcePlus extends XlsSource {
  
  use ImportTrait;
  
  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, MigrationInterface $migration) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $migration);

    // XlsSource 的 prepareColumns 是 private 函数，没法替换，需要重新构造 columns.
    // ImportFormSelectSheet will make columns duplicate
    // 如果用户通过界面设置了到导入的字段
    if (isset($migration->configuration['source']['columns'])) {
      $this->columns = $migration->configuration['source']['columns'];
      $this->prepareColumns();
    }

    // optional_keys 与 keys 的差别：options_key在栏目映射界面可以不选。
    if (isset($this->configuration['optional_keys'])) {
      $this->configuration['keys'] = array_merge($this->configuration['keys'], $this->configuration['optional_keys']);
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

    // 获取用户在字段映射界面删除的栏目.
    // 即：在 $this->migration->source['columns'] 存在，但是 $this->columns 不存在的栏目.
    // 自动安装时，导入配置里只定义了待修改或添加的列,默认列会被下面的代码删除掉
    $removed_columns = [];
    if (!isset($this->configuration['auto_install'])) {
      $source_configuration = $this->migration->getSourceConfiguration();
      $entity_type_id = $source_configuration['entity_type_id'];
      if (array_key_exists('type', $source_configuration)) {
        $bundle = $source_configuration['type'];
      }
      else {
        $bundle = $entity_type_id;
      }
      $columns = $this->getFieldConfigColumns($entity_type_id, $bundle);
      $columns = array_merge($this->getColumns(), $columns);
      $columns = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($columns)), FALSE); // @see \Drupal\migrate\Plugin\migrate\process\Flatten
      
      $selected_columns = iterator_to_array(new \RecursiveIteratorIterator(new \RecursiveArrayIterator($this->columns)), FALSE);
      $removed_columns = array_diff($columns, $selected_columns);
    }

    $columns = [];
    $iterator = $this->file
      ->getActiveSheet()
      ->getRowIterator($this->configuration['header_row'], $this->configuration['header_row']);
    /** @var \PhpOffice\PhpSpreadsheet\Cell\Cell $cell */
    // 获取表头栏目
    foreach ($iterator->current()->getCellIterator() as $cell) {
      // 处理表头换行
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

        // 放入自定义字段需要的数据.
        // 如果是客户在字段映射删除的栏目，不要放入.
        if (!isset($columns[$cell_column]) && !in_array($header, $removed_columns)) {
          $columns[$cell_column] = $header;
        }
      }
    }

    // 将 excel 栏目名称替换为导入处理的栏目名称.
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

          // 如果为空，不要覆盖配置文件中source设置的值.
          if ($value !== null) {
            $row_data[$column] = $value;
          }
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
