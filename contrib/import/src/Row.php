<?php

namespace Drupal\import;

use Drupal\migrate\Row as RowBase;

class Row extends RowBase {

  /**
   * {@inheritdoc}
   */
  public function __construct(array $values = [], array $source_ids = [], $is_stub = FALSE) {
    // 当 keys 的数据不存在，父类会停止导入。
    // 为不存在的 keys 提供空值，具体的处理逻辑交给 process 配置进行处理。
    foreach (array_keys($source_ids) as $id) {
      if (!isset($values[$id])) {
        $values[$id] = '';
      }
    }

    parent::__construct($values, $source_ids, $is_stub);
  }

}
