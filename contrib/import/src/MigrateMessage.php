<?php

namespace Drupal\import;

use Drupal\migrate\MigrateMessage as MigrateMessageBase;

class MigrateMessage extends MigrateMessageBase {

  /**
   * {@inheritdoc}
   */
  public function display($message, $type = 'status') {
    parent::display($message, $type);
    \Drupal::messenger()->addWarning($message);
  }

}
