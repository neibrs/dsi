<?php

namespace Drupal\entity_plus\Field;

use Drupal\Core\Field\FormatterPluginManager as FormatterPluginManagerBase;

class FormatterPluginManager extends FormatterPluginManagerBase {

  /**
   * {@inheritdoc}
   */
  public function prepareConfiguration($field_type, array $configuration) {
    $configuration += ['label' => 'inline'];
    $configuration = parent::prepareConfiguration($field_type, $configuration);

    return $configuration;
  }

}
