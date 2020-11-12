<?php

namespace Drupal\eabax_core\Plugin\Field\FieldWidget;

use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget as StringTextfieldWidgetBase;

/**
 * Decrease the default size.
 */
class StringTextfieldWidget extends StringTextfieldWidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    $settings =  parent::defaultSettings();

    $settings['size'] = 40;

    return $settings;
  }

}
