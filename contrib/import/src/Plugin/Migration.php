<?php

namespace Drupal\import\Plugin;

use Drupal\migrate\Plugin\Migration as MigrationBase;

class Migration extends MigrationBase implements MigrationInterface {

  /**
   * {@inheritdoc}
   */
  public function setSourceConfiguration(array $configuration) {
    $this->source = $configuration;
    return $this;
  }
}