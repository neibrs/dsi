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

    if ($field_type == 'datetime' || $field_type == 'daterange') {
      $configuration['settings']['format_type'] = 'html_date';
    }

    return $configuration;
  }

}
